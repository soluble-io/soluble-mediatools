<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Util\Assert\BitrateAssertionsTrait;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\Converter\FFMpegCLIValueInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\SeekTime;

class VideoConversionParams implements ConversionParamsInterface
{
    use BitrateAssertionsTrait;

    /** @var array<string, bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface> */
    protected $params = [];

    /**
     * @param array<string, bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface> $params
     *
     * @throws InvalidArgumentException in case of unsupported option
     */
    public function __construct($params = [])
    {
        $this->ensureSupportedParams($params);
        $this->params = $params;
    }

    public function withVideoCodec(string $videoCodec): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_CODEC => $videoCodec,
        ]));
    }

    public function withVideoFilter(VideoFilterInterface $videoFilter): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_FILTER => $videoFilter,
        ]));
    }

    public function withAudioCodec(string $audioCodec): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_AUDIO_CODEC => $audioCodec,
        ]));
    }

    /**
     * Set TileColumns (VP9 - to use in conjunction with FrameParallel).
     *
     * Tiling splits the video frame into multiple columns,
     * which slightly reduces quality but speeds up encoding performance.
     * Tiles must be at least 256 pixels wide, so there is a limit to how many tiles can be used.
     * Depending upon the number of tiles and the resolution of the tmp frame, more CPU threads may be useful.
     *
     * Generally speaking, there is limited value to multiple threads when the tmp frame size is very small.
     *
     * @see VideoConversionParams::withFrameParallel()
     */
    public function withTileColumns(int $tileColumns): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_TILE_COLUMNS => $tileColumns,
        ]));
    }

    /**
     * Set FrameParallel (VP9 - to use in conjunction with TileColumns).
     *
     * @see VideoConversionParams::withTileColumns()
     */
    public function withFrameParallel(int $frameParallel): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_FRAME_PARALLEL => $frameParallel,
        ]));
    }

    /**
     * Set KeyFrameSpacing (VP9).
     *
     * It is recommended to allow up to 240 frames of video between keyframes (8 seconds for 30fps content).
     * Keyframes are video frames which are self-sufficient; they don't rely upon any other frames to render
     * but they tend to be larger than other frame types.
     *
     * For web and mobile playback, generous spacing between keyframes allows the encoder to choose the best
     * placement of keyframes to maximize quality.
     */
    public function withKeyframeSpacing(int $keyframeSpacing): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_KEYFRAME_SPACING => $keyframeSpacing,
        ]));
    }

    /**
     * Set compression level (Constant Rate Factor).
     */
    public function withCrf(int $crf): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_CRF => $crf,
        ]));
    }

    public function withPixFmt(string $pixFmt): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_PIX_FMT => $pixFmt,
        ]));
    }

    public function withPreset(string $preset): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_PRESET => $preset,
        ]));
    }

    public function withSpeed(int $speed): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_SPEED => $speed,
        ]));
    }

    public function withThreads(int $threads): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_THREADS => $threads,
        ]));
    }

    public function withTune(string $tune): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_TUNE => $tune,
        ]));
    }

    /**
     * If true, add streamable options for mp4 container (-movflags +faststart).
     */
    public function withStreamable(bool $streamable): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_STREAMABLE => $streamable,
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

        return new self(array_merge($this->params, [
            self::PARAM_AUDIO_BITRATE => $bitrate,
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

        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_BITRATE => $bitrate,
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

        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_MIN_BITRATE => $minBitrate,
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

        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_MAX_BITRATE => $maxBitrate,
        ]));
    }

    /**
     * Whether to overwrite output file if it exists.
     */
    public function withOverwriteFile(): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OVERWRITE_FILE => true
        ]));
    }

    public function withQuality(string $quality): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_QUALITY => $quality,
        ]));
    }

    public function withOutputFormat(string $outputFormat): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OUTPUT_FORMAT => $outputFormat,
        ]));
    }

    public function isParamValid(string $paramName): bool
    {
        return in_array($paramName, self::BUILTIN_PARAMS, true);
    }

    /**
     * Return the internal array holding params.
     *
     * @return array<string,bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface>
     */
    public function toArray(): array
    {
        return $this->params;
    }

    /**
     * @param string                                                            $paramName
     * @param bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface|null $defaultValue if param does not exists set this one
     *
     * @return bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface|null
     */
    public function getParam(string $paramName, $defaultValue = null)
    {
        return $this->params[$paramName] ?? $defaultValue;
    }

    public function hasParam(string $paramName): bool
    {
        return array_key_exists($paramName, $this->params);
    }

    public function withFilter(string $filter): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_FILTER => $filter,
        ]));
    }

    public function withSeekStart(SeekTime $seekTimeStart): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_SEEK_START => $seekTimeStart,
        ]));
    }

    public function withSeekEnd(SeekTime $seekTimeEnd): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_SEEK_END => $seekTimeEnd,
        ]));
    }

    public function withNoAudio(): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_NOAUDIO => true,
        ]));
    }

    public function withVideoFrames(int $numberOfFrames): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_FRAMES => $numberOfFrames,
        ]));
    }

    public function withVideoQualityScale(int $qualityScale): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_QUALITY_SCALE => $qualityScale,
        ]));
    }

    /**
     * Ensure that all params are supported.
     *
     * @param array<string, bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface> $params
     *
     * @throws InvalidArgumentException in case of unsupported option
     */
    protected function ensureSupportedParams(array $params): void
    {
        foreach ($params as $paramName => $paramValue) {
            if (!$this->isParamValid($paramName)) {
                throw new InvalidArgumentException(
                    sprintf('Unsupported param "%s" given.', $paramName)
                );
            }
        }
    }
}
