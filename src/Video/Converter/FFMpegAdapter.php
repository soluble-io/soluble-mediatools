<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Converter;

use Soluble\MediaTools\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Exception\UnsupportedParamException;
use Soluble\MediaTools\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Util\PlatformNullFile;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

class FFMpegAdapter implements AdapterInterface
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
            ConversionParamsInterface::PARAM_OUTPUT_FORMAT => [
                'pattern' => '-f %s',
            ],

            ConversionParamsInterface::PARAM_VIDEO_CODEC => [
                'pattern' => '-c:v %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_BITRATE => [
                'pattern' => '-b:v %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_MIN_BITRATE => [
                'pattern' => '-minrate %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_MAX_BITRATE => [
                'pattern' => '-maxrate %s',
            ],

            ConversionParamsInterface::PARAM_AUDIO_CODEC => [
                'pattern' => '-c:a %s',
            ],
            ConversionParamsInterface::PARAM_AUDIO_BITRATE => [
                'pattern' => '-b:a %s',
            ],
            ConversionParamsInterface::PARAM_PIX_FMT => [
                'pattern' => '-pix_fmt %s',
            ],
            ConversionParamsInterface::PARAM_PRESET => [
                'pattern' => '-preset %s',
            ],
            ConversionParamsInterface::PARAM_SPEED => [
                'pattern' => '-speed %d',
            ],
            ConversionParamsInterface::PARAM_THREADS => [
                'pattern' => '-threads %d',
            ],
            ConversionParamsInterface::PARAM_KEYFRAME_SPACING => [
                'pattern' => '-g %d',
            ],
            ConversionParamsInterface::PARAM_QUALITY => [
                'pattern' => '-quality %s',
            ],

            ConversionParamsInterface::PARAM_VIDEO_QUALITY_SCALE => [
                'pattern' => '-qscale:v %d',
            ],

            ConversionParamsInterface::PARAM_CRF => [
                'pattern' => '-crf %d',
            ],
            ConversionParamsInterface::PARAM_STREAMABLE => [
                'pattern' => '-movflags +faststart',
            ],

            ConversionParamsInterface::PARAM_FRAME_PARALLEL => [
                'pattern' => '-frame-parallel %s',
            ],
            ConversionParamsInterface::PARAM_TILE_COLUMNS => [
                'pattern' => '-tile-columns %s',
            ],
            ConversionParamsInterface::PARAM_TUNE => [
                'pattern' => '-tune %s',
            ],
            ConversionParamsInterface::PARAM_VIDEO_FILTER => [
                'pattern' => '-vf %s',
            ],
            ConversionParamsInterface::PARAM_OVERWRITE => [
                'pattern' => '-y',
            ],
            ConversionParamsInterface::PARAM_VIDEO_FRAMES => [
                'pattern' => '-frames:v %d',
            ],
            ConversionParamsInterface::PARAM_NOAUDIO => [
                'pattern' => '-an',
            ],

            ConversionParamsInterface::PARAM_SEEK_START => [
                'pattern' => '-ss %s',
            ],

            ConversionParamsInterface::PARAM_SEEK_END => [
                'pattern' => '-to %s',
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

        // Add default overwrite option if not set
        $overwriteParam = ConversionParamsInterface::PARAM_OVERWRITE;
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
}
