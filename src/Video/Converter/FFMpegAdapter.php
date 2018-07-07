<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Converter;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Exception\UnsupportedParamException;
use Soluble\MediaTools\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Util\PlatformNullFile;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

class FFMpegAdapter implements AdapterInterface
{
    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    public function __construct(FFMpegConfig $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getParamsOptions(): array
    {
        return [
            ConversionParamsInterface::PARAM_OUTPUT_FORMAT => [
                'cli_pattern' => '-f %s',
            ],

            ConversionParamsInterface::PARAM_VIDEO_CODEC => [
                'cli_pattern' => '-c:v %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_BITRATE => [
                'cli_pattern' => '-b:v %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_MIN_BITRATE => [
                'cli_pattern' => '-minrate %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_MAX_BITRATE => [
                'cli_pattern' => '-maxrate %s',
            ],

            ConversionParamsInterface::PARAM_AUDIO_CODEC => [
                'cli_pattern' => '-c:a %s',
            ],
            ConversionParamsInterface::PARAM_AUDIO_BITRATE => [
                'cli_pattern' => '-b:a %s',
            ],
            ConversionParamsInterface::PARAM_PIX_FMT => [
                'cli_pattern' => '-pix_fmt %s',
            ],
            ConversionParamsInterface::PARAM_PRESET => [
                'cli_pattern' => '-preset %s',
            ],
            ConversionParamsInterface::PARAM_SPEED => [
                'cli_pattern' => '-speed %d',
            ],
            ConversionParamsInterface::PARAM_THREADS => [
                'cli_pattern' => '-threads %d',
            ],
            ConversionParamsInterface::PARAM_KEYFRAME_SPACING => [
                'cli_pattern' => '-g %d',
            ],
            ConversionParamsInterface::PARAM_QUALITY => [
                'cli_pattern' => '-quality %s',
            ],

            ConversionParamsInterface::PARAM_VIDEO_QUALITY_SCALE => [
                'cli_pattern' => '-qscale:v %d',
            ],

            ConversionParamsInterface::PARAM_CRF => [
                'cli_pattern' => '-crf %d',
            ],
            ConversionParamsInterface::PARAM_STREAMABLE => [
                'cli_pattern' => '-movflags +faststart',
            ],

            ConversionParamsInterface::PARAM_FRAME_PARALLEL => [
                'cli_pattern' => '-frame-parallel %s',
            ],
            ConversionParamsInterface::PARAM_TILE_COLUMNS => [
                'cli_pattern' => '-tile-columns %s',
            ],
            ConversionParamsInterface::PARAM_TUNE => [
                'cli_pattern' => '-tune %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_FILTER => [
                'cli_pattern' => '-vf %s',
            ],
            ConversionParamsInterface::PARAM_OVERWRITE => [
                'cli_pattern' => '-y',
            ],
            ConversionParamsInterface::PARAM_VIDEO_FRAMES => [
                'cli_pattern' => '-frames:v %d',
            ],
            ConversionParamsInterface::PARAM_NOAUDIO => [
                'cli_pattern' => '-an',
            ],

            ConversionParamsInterface::PARAM_SEEK_START => [
                'cli_pattern' => '-ss %s',
            ],

            ConversionParamsInterface::PARAM_SEEK_END => [
                'cli_pattern' => '-to %s',
            ],
        ];
    }

    /**
     * @return array<string, string>
     *
     * @throws UnsupportedParamException
     * @throws UnsupportedParamValueException
     */
    public function getMappedConversionParams(ConversionParamsInterface $conversionParams): array
    {
        $args             = [];
        $supportedOptions = $this->getParamsOptions();

        foreach ($conversionParams->toArray() as $paramName => $value) {
            if (!array_key_exists($paramName, $supportedOptions)) {
                throw new UnsupportedParamException(
                    sprintf(
                        'FFMpegAdapter does not support param \'%s\'',
                        $paramName
                    )
                );
            }
            $ffmpeg_pattern = $supportedOptions[$paramName]['cli_pattern'];
            if (is_bool($value)) {
                $args[$paramName] = $ffmpeg_pattern;
            } elseif ($value instanceof FFMpegCLIValueInterface) {
                $args[$paramName] = sprintf($ffmpeg_pattern, $value->getFFmpegCLIValue());
            } elseif ($value instanceof FFMpegVideoFilterInterface) {
                $args[$paramName] = sprintf($ffmpeg_pattern, $value->getFFmpegCLIValue());
            } elseif (is_string($value) || is_int($value)) {
                $args[$paramName] = sprintf($ffmpeg_pattern, $value);
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
}
