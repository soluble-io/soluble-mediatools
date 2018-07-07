<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Converter\Adapter;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Video\ConversionParamsInterface;

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
    public static function getParamsOptions(): array
    {
        return [
            ConversionParamsInterface::PARAM_OUTPUT_FORMAT => [
                'cli_pattern' => '-f %s',
            ],

            ConversionParamsInterface::PARAM_VIDEO_CODEC => [
                'cli_pattern' => '-vcodec %s',
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
                'cli_pattern' => '-acodec %s',
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
                'cli_pattern' => '-speed %s',
            ],
            ConversionParamsInterface::PARAM_THREADS => [
                'cli_pattern' => '-threads %s',
            ],

            ConversionParamsInterface::PARAM_KEYFRAME_SPACING => [
                'cli_pattern' => '-g %s',
            ],
            ConversionParamsInterface::PARAM_QUALITY => [
                'cli_pattern' => '-quality %s',
            ],
            ConversionParamsInterface::PARAM_CRF => [
                'cli_pattern' => '-crf %s',
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
        ];
    }
}
