<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Converter\Adapter;

use Soluble\MediaTools\Video\Converter\ParamsInterface;

class FFMpegAdapter implements AdapterInterface
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function getParamsOptions(): array
    {
        return [
            ParamsInterface::PARAM_OUTPUT_FORMAT => [
                'cli_pattern' => '-f %s',
            ],

            ParamsInterface::PARAM_VIDEO_CODEC => [
                'cli_pattern' => '-vcodec %s',
            ],
            ParamsInterface::PARAM_VIDEO_BITRATE => [
                'cli_pattern' => '-b:v %s',
            ],
            ParamsInterface::PARAM_VIDEO_MIN_BITRATE => [
                'cli_pattern' => '-minrate %s',
            ],
            ParamsInterface::PARAM_VIDEO_MAX_BITRATE => [
                'cli_pattern' => '-maxrate %s',
            ],

            ParamsInterface::PARAM_AUDIO_CODEC => [
                'cli_pattern' => '-acodec %s',
            ],
            ParamsInterface::PARAM_AUDIO_BITRATE => [
                'cli_pattern' => '-b:a %s',
            ],
            ParamsInterface::PARAM_PIX_FMT => [
                'cli_pattern' => '-pix_fmt %s',
            ],
            ParamsInterface::PARAM_PRESET => [
                'cli_pattern' => '-preset %s',
            ],
            ParamsInterface::PARAM_SPEED => [
                'cli_pattern' => '-speed %s',
            ],
            ParamsInterface::PARAM_THREADS => [
                'cli_pattern' => '-threads %s',
            ],

            ParamsInterface::PARAM_KEYFRAME_SPACING => [
                'cli_pattern' => '-g %s',
            ],
            ParamsInterface::PARAM_QUALITY => [
                'cli_pattern' => '-quality %s',
            ],
            ParamsInterface::PARAM_CRF => [
                'cli_pattern' => '-crf %s',
            ],
            ParamsInterface::PARAM_STREAMABLE => [
                'cli_pattern' => '-movflags +faststart',
            ],

            ParamsInterface::PARAM_FRAME_PARALLEL => [
                'cli_pattern' => '-frame-parallel %s',
            ],
            ParamsInterface::PARAM_TILE_COLUMNS => [
                'cli_pattern' => '-tile-columns %s',
            ],
            ParamsInterface::PARAM_TUNE => [
                'cli_pattern' => '-tune %s',
            ],
            ParamsInterface::PARAM_VIDEO_FILTER => [
                'cli_pattern' => '-vf %s',
            ],
        ];
    }
}
