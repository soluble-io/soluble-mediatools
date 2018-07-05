<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Filter\Video\VideoFilterInterface;

class VideoConvertParams
{
    public const OPTION_VIDEO_CODEC       = 'VIDEO_CODEC';
    public const OPTION_VIDEO_BITRATE     = 'VIDEO_BITRATE';
    public const OPTION_VIDEO_MIN_BITRATE = 'VIDEO_MIN_BITRATE';
    public const OPTION_VIDEO_MAX_BITRATE = 'VIDEO_MAX_BITRATE';
    public const OPTION_VIDEO_FILTER      = 'VIDEO_FILTER';
    public const OPTION_AUDIO_CODEC       = 'AUDIO_CODEC';
    public const OPTION_AUDIO_BITRATE     = 'AUDIO_BITRATE';
    public const OPTION_CRF               = 'CRF';
    public const OPTION_PIX_FMT           = 'PIX_FMT';
    public const OPTION_PRESET            = 'PRESET';
    public const OPTION_TUNE              = 'TUNE';
    public const OPTION_STREAMABLE        = 'STREAMABLE'; // h264
    public const OPTION_QUALITY           = 'QUALITY'; // vp9 only
    public const OPTION_OUTPUT_FORMAT     = 'OUTPUT_FORMAT';
    public const OPTION_FRAME_PARALLEL    = 'FRAME_PARALLEL';
    public const OPTION_TILE_COLUMNS      = 'TILE_COLUMNS';
    public const OPTION_SPEED             = 'SPEED'; // vp9
    public const OPTION_THREADS           = 'THREADS'; // vp9
    public const OPTION_KEYFRAME_SPACING  = 'KEYFRAME_SPACING'; // vp9

    public const SUPPORTED_OPTIONS = [
        self::OPTION_OUTPUT_FORMAT => [
            'ffmpeg_pattern' => '-f %s',
        ],

        self::OPTION_VIDEO_CODEC => [
            'ffmpeg_pattern' => '-vcodec %s',
        ],
        self::OPTION_VIDEO_BITRATE => [
            'ffmpeg_pattern' => '-b:v %s',
        ],
        self::OPTION_VIDEO_MIN_BITRATE => [
            'ffmpeg_pattern' => '-minrate %s',
        ],
        self::OPTION_VIDEO_MAX_BITRATE => [
            'ffmpeg_pattern' => '-maxrate %s',
        ],

        self::OPTION_AUDIO_CODEC => [
            'ffmpeg_pattern' => '-acodec %s',
        ],
        self::OPTION_AUDIO_BITRATE => [
            'ffmpeg_pattern' => '-b:a %s',
        ],
        self::OPTION_PIX_FMT => [
            'ffmpeg_pattern' => '-pix_fmt %s',
        ],
        self::OPTION_PRESET => [
            'ffmpeg_pattern' => '-preset %s',
        ],
        self::OPTION_SPEED => [
            'ffmpeg_pattern' => '-speed %s',
        ],
        self::OPTION_THREADS => [
            'ffmpeg_pattern' => '-threads %s',
        ],

        self::OPTION_KEYFRAME_SPACING => [
            'ffmpeg_pattern' => '-g %s',
        ],
        self::OPTION_QUALITY => [
            'ffmpeg_pattern' => '-quality %s',
        ],
        self::OPTION_CRF => [
            'ffmpeg_pattern' => '-crf %s',
        ],
        self::OPTION_STREAMABLE => [
            'ffmpeg_pattern' => '-movflags +faststart',
        ],

        self::OPTION_FRAME_PARALLEL => [
            'ffmpeg_pattern' => '-frame-parallel %s',
        ],
        self::OPTION_TILE_COLUMNS => [
            'ffmpeg_pattern' => '-tile-columns %s',
        ],
        self::OPTION_TUNE => [
            'ffmpeg_pattern' => '-tune %s',
        ],
        self::OPTION_VIDEO_FILTER => [
            'ffmpeg_pattern' => '-vf %s',
        ],
    ];

    /** @var array<string, bool|string|int|VideoFilterInterface> */
    protected $options = [];

    /**
     * @param array<string, bool|string|int|VideoFilterInterface> $options
     *
     * @throws InvalidArgumentException in case of unsupported option
     */
    public function __construct($options = [])
    {
        $this->ensureSupportedOptions($options);
        $this->options = $options;
    }

    public function isOptionValid(string $optionName): bool
    {
        return array_key_exists($optionName, self::SUPPORTED_OPTIONS);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string                                    $option
     * @param bool|string|int|VideoFilterInterface|null $default if options does not exists set this one
     *
     * @return bool|string|int|VideoFilterInterface|null
     */
    public function getOption(string $option, $default = null)
    {
        return $this->options[$option] ?? $default;
    }

    public function hasOption(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    public function withVideoCodec(string $videoCodec): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_VIDEO_CODEC => $videoCodec,
        ]));
    }

    public function withVideoFilter(VideoFilterInterface $videoFilter): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_VIDEO_FILTER => $videoFilter,
        ]));
    }

    public function withAudioCodec(string $audioCodec): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_AUDIO_CODEC => $audioCodec,
        ]));
    }

    /**
     * Tiling splits the video frame into multiple columns,
     * which slightly reduces quality but speeds up encoding performance.
     * Tiles must be at least 256 pixels wide, so there is a limit to how many tiles can be used.
     * Depending upon the number of tiles and the resolution of the output frame, more CPU threads may be useful.
     *
     * Generally speaking, there is limited value to multiple threads when the output frame size is very small.
     */
    public function withTileColumns(int $tileColumns): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_TILE_COLUMNS => $tileColumns,
        ]));
    }

    /**
     * VP9 ?
     * It is recommended to allow up to 240 frames of video between keyframes (8 seconds for 30fps content).
     * Keyframes are video frames which are self-sufficient; they don't rely upon any other frames to render
     * but they tend to be larger than other frame types.
     * For web and mobile playback, generous spacing between keyframes allows the encoder to choose the best
     * placement of keyframes to maximize quality.
     */
    public function withKeyframeSpacing(int $keyframeSpacing): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_KEYFRAME_SPACING => $keyframeSpacing,
        ]));
    }

    public function withFrameParallel(int $frameParallel): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_FRAME_PARALLEL => $frameParallel,
        ]));
    }

    public function withCrf(int $crf): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_CRF => $crf,
        ]));
    }

    public function withPixFmt(string $pixFmt): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_PIX_FMT => $pixFmt,
        ]));
    }

    public function withPreset(string $preset): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_PRESET => $preset,
        ]));
    }

    public function withSpeed(int $speed): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_SPEED => $speed,
        ]));
    }

    public function withThreads(int $threads): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_THREADS => $threads,
        ]));
    }

    public function withTune(string $tune): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_TUNE => $tune,
        ]));
    }

    /**
     * If true, add streamable options for mp4 container (-movflags +faststart).
     */
    public function withStreamable(bool $streamable): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_STREAMABLE => $streamable,
        ]));
    }

    /**
     * @param string $bitrate Bitrate with optional unit: 1000000, 1000k or 1M
     *
     * @throws InvalidArgumentException if bitrate value is invalid
     */
    public function withAudioBitrate(string $bitrate): self
    {
        $this->ensureValidBitRateUnit($bitrate);

        return new self(array_merge($this->options, [
            self::OPTION_AUDIO_BITRATE => $bitrate,
        ]));
    }

    /**
     * @param string $bitrate Bitrate or target bitrate with optional unit: 1000000, 1000k or 1M
     *
     * @throws InvalidArgumentException if bitrate value is invalid
     */
    public function withVideoBitrate(string $bitrate): self
    {
        $this->ensureValidBitRateUnit($bitrate);

        return new self(array_merge($this->options, [
            self::OPTION_VIDEO_BITRATE => $bitrate,
        ]));
    }

    /**
     * @param string $minBitrate Bitrate with optional unit: 1000000, 1000k or 1M
     *
     * @throws InvalidArgumentException if bitrate value is invalid
     */
    public function withVideoMinBitrate(string $minBitrate): self
    {
        $this->ensureValidBitRateUnit($minBitrate);

        return new self(array_merge($this->options, [
            self::OPTION_VIDEO_MIN_BITRATE => $minBitrate,
        ]));
    }

    /**
     * @param string $maxBitrate Bitrate with optional unit: 1000000, 1000k or 1M
     *
     * @throws InvalidArgumentException if bitrate value is invalid
     */
    public function withVideoMaxBitrate(string $maxBitrate): self
    {
        $this->ensureValidBitRateUnit($maxBitrate);

        return new self(array_merge($this->options, [
            self::OPTION_VIDEO_MAX_BITRATE => $maxBitrate,
        ]));
    }

    public function withQuality(string $quality): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_QUALITY => $quality,
        ]));
    }

    public function withOutputFormat(string $outputFormat): self
    {
        return new self(array_merge($this->options, [
            self::OPTION_OUTPUT_FORMAT => $outputFormat,
        ]));
    }

    /**
     * @return array<string, string>
     */
    public function getFFMpegArguments(): array
    {
        $args = [];
        foreach ($this->options as $key => $value) {
            $ffmpeg_pattern = self::SUPPORTED_OPTIONS[$key]['ffmpeg_pattern'];
            if (is_bool($value)) {
                $args[$key] = $ffmpeg_pattern;
            } elseif ($value instanceof VideoFilterInterface) {
                $args[$key] = sprintf($ffmpeg_pattern, $value->getFFmpegCLIValue());
            } else {
                $args[$key] = sprintf($ffmpeg_pattern, $value);
            }
        }

        return $args;
    }

    /**
     * Ensure that a bitrate is valid (optional unit: k or M ).
     *
     * @throws InvalidArgumentException
     */
    protected function ensureValidBitRateUnit(string $bitrate): void
    {
        if (preg_match('/^\d+(k|M)?$/i', $bitrate) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s"', $bitrate));
        }
    }

    /**
     * Ensure that all options are supported.
     *
     * @throws InvalidArgumentException in case of unsupported option
     */
    protected function ensureSupportedOptions(array $options): void
    {
        foreach (array_keys($options) as $optionName) {
            if (!$this->isOptionValid($optionName)) {
                throw new InvalidArgumentException(
                    sprintf('Unsupported option "%s" given.', $optionName)
                );
            }
        }
    }
}
