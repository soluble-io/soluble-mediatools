<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Adapter;

use Soluble\MediaTools\Common\Exception\InvalidArgumentException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Common\IO\PlatformNullFile;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\VideoConvertParamsInterface;

class FFMpegAdapter implements ConverterAdapterInterface
{
    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    public function __construct(FFMpegConfigInterface $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getParamsOptions(): array
    {
        return [
            VideoConvertParamsInterface::PARAM_OUTPUT_FORMAT => [
                'pattern' => '-f %s',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_CODEC => [
                'pattern' => '-c:v %s',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_BITRATE => [
                'pattern' => '-b:v %s',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_MIN_BITRATE => [
                'pattern' => '-minrate %s',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_MAX_BITRATE => [
                'pattern' => '-maxrate %s',
            ],
            VideoConvertParamsInterface::PARAM_AUDIO_CODEC => [
                'pattern' => '-c:a %s',
            ],
            VideoConvertParamsInterface::PARAM_AUDIO_BITRATE => [
                'pattern' => '-b:a %s',
            ],
            VideoConvertParamsInterface::PARAM_PIX_FMT => [
                'pattern' => '-pix_fmt %s',
            ],
            VideoConvertParamsInterface::PARAM_PRESET => [
                'pattern' => '-preset %s',
            ],
            VideoConvertParamsInterface::PARAM_SPEED => [
                'pattern' => '-speed %d',
            ],
            VideoConvertParamsInterface::PARAM_THREADS => [
                'pattern' => '-threads %d',
            ],
            VideoConvertParamsInterface::PARAM_KEYFRAME_SPACING => [
                'pattern' => '-g %d',
            ],
            VideoConvertParamsInterface::PARAM_QUALITY => [
                'pattern' => '-quality %s',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_QUALITY_SCALE => [
                'pattern' => '-qscale:v %d',
            ],
            VideoConvertParamsInterface::PARAM_CRF => [
                'pattern' => '-crf %d',
            ],
            VideoConvertParamsInterface::PARAM_STREAMABLE => [
                'pattern' => '-movflags +faststart',
            ],
            VideoConvertParamsInterface::PARAM_FRAME_PARALLEL => [
                'pattern' => '-frame-parallel %s',
            ],
            VideoConvertParamsInterface::PARAM_TILE_COLUMNS => [
                'pattern' => '-tile-columns %s',
            ],
            VideoConvertParamsInterface::PARAM_TUNE => [
                'pattern' => '-tune %s',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_FILTER => [
                'pattern' => '-vf %s',
            ],
            VideoConvertParamsInterface::PARAM_OVERWRITE => [
                'pattern' => '-y',
            ],
            VideoConvertParamsInterface::PARAM_VIDEO_FRAMES => [
                'pattern' => '-frames:v %d',
            ],
            VideoConvertParamsInterface::PARAM_NOAUDIO => [
                'pattern' => '-an',
            ],
            VideoConvertParamsInterface::PARAM_SEEK_START => [
                'pattern' => '-ss %s',
            ],
            VideoConvertParamsInterface::PARAM_SEEK_END => [
                'pattern' => '-to %s',
            ],
            VideoConvertParamsInterface::PARAM_PASSLOGFILE => [
                'pattern' => '-passlogfile %s',
            ],
            VideoConvertParamsInterface::PARAM_PASS => [
                'pattern' => '-pass %s',
            ],
        ];
    }

    /**
     * @return array<string, string>
     *
     * @throws UnsupportedParamException
     * @throws UnsupportedParamValueException
     */
    public function getMappedConversionParams(VideoConvertParamsInterface $conversionParams): array
    {
        $args             = [];
        $supportedOptions = $this->getParamsOptions();

        // Add default overwrite option if not set
        $overwriteParam = VideoConvertParamsInterface::PARAM_OVERWRITE;
        if (!$conversionParams->hasParam($overwriteParam)) {
            $conversionParams = $conversionParams->withBuiltInParam(
                $overwriteParam,
                true
            );
        }

        foreach ($conversionParams->toArray() as $paramName => $value) {
            if (!array_key_exists($paramName, $supportedOptions)) {
                throw new UnsupportedParamException(
                    sprintf(
                        'FFMpegAdapter does not support param \'%s\'',
                        $paramName
                    )
                );
            }
            $pattern = $supportedOptions[$paramName]['pattern'];
            if (is_bool($value)) {
                $args[$paramName] = $value ? $pattern : '';
            } elseif ($value instanceof FFMpegCLIValueInterface) {
                // Will test also FFMpegVideoFilterInterface
                $args[$paramName] = sprintf($pattern, $value->getFFmpegCLIValue());
            } elseif (is_string($value) || is_int($value)) {
                $args[$paramName] = sprintf($pattern, $value);
            } else {
                throw new UnsupportedParamValueException(
                    sprintf(
                        'Param \'%s\' has an unsupported type: \'%s\'',
                        $paramName,
                        is_object($value) ? get_class($value) : gettype($value)
                    )
                );
            }
        }

        return $args;
    }

    /**
     * @param array<int|string, string>    $arguments
     * @param string|null                  $inputFile  if <null> will not prepend '-i inputFile' in args
     * @param null|string|PlatformNullFile $outputFile
     *
     * @throws InvalidArgumentException
     */
    public function getCliCommand(array $arguments, ?string $inputFile, $outputFile = null): string
    {
        $inputArg = ($inputFile !== null && $inputFile !== '')
                        ? sprintf('-i %s', escapeshellarg($inputFile))
                        : '';

        $outputArg = '';
        if ($outputFile instanceof PlatformNullFile) {
            $outputArg = $outputFile->getNullFile();
        } elseif (is_string($outputFile)) {
            $outputArg = sprintf('%s', escapeshellarg($outputFile));
        } elseif ($outputFile !== null) {
            throw new InvalidArgumentException(sprintf(
                'Output file must be either a non empty string, null or PlatformNullFile (type %s)',
                gettype($outputFile)
            ));
        }

        $ffmpegCmd = trim(sprintf(
            '%s %s %s %s',
            $this->ffmpegConfig->getBinary(),
            $inputArg,
            implode(' ', $arguments),
            $outputArg
        ));

        return $ffmpegCmd;
    }

    public function getDefaultThreads(): ?int
    {
        return $this->ffmpegConfig->getThreads();
    }
}
