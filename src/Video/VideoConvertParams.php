<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Assert\BitrateAssertionsTrait;
use Soluble\MediaTools\Video\Adapter\FFMpegCLIValueInterface;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\UnsetParamException;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;

class VideoConvertParams implements VideoConvertParamsInterface
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
     * @see self::withFrameParallel()
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
     * @see self::withTileColumns()
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
     * The Constant Rate Factor (CRF) setting for the x264, x265 and vp9.
     *
     * encoders.
     *
     * - h264: You can set the values between 0 and 51, where lower values would result in better quality,
     *         at the expense of higher file sizes. Higher values mean more compression,
     *         but at some point you will notice the quality degradation.
     *         For x264, sane values are between 18 and 28. The default is 23, so you can use this as a starting point.
     *
     * - vp9:  The CRF value can be from 0–63. Lower values mean better quality. Recommended values range from 15–35,
     *         with 31 being recommended for 1080p HD video
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
     * Add with overwrite option (default).
     *
     * @see self::withNoOverwrite()
     */
    public function withOverwrite(): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OVERWRITE => true
        ]));
    }

    /**
     * Add protection against output file overwriting.
     *
     * @see self::witoOverwrite()
     */
    public function withNoOverwrite(): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OVERWRITE => false
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

    /**
     * Set the video encoder quality scale. (-qscale:v <int>, alias to -q:v <int>).
     *
     * @param int $qualityScale a number interpreted by the encoder, generally 1-5
     *
     * @see self::withQuality()
     */
    public function withVideoQualityScale(int $qualityScale): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_QUALITY_SCALE => $qualityScale,
        ]));
    }

    /**
     * Setting auto-alt-ref and lag-in-frames >= 12 will turn on VP9's alt-ref frames, a VP9 feature that enhances quality.
     */
    public function withAutoAltRef(int $autoAltRef): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_AUTO_ALT_REF => $autoAltRef,
        ]));
    }

    /**
     * Setting auto-alt-ref and lag-in-frames >= 12 will turn on VP9's alt-ref frames, a VP9 feature that enhances quality.
     */
    public function withLagInFrames(int $lagInFrames): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_LAG_IN_FRAMES => $lagInFrames,
        ]));
    }

    /**
     * Set the pass number.
     */
    public function withPass(int $passNumber): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_PASS => $passNumber,
        ]));
    }

    /**
     * Set the passlogfile (only makes sense for multipass conversions).
     */
    public function withPassLogFile(string $passLogFile): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_PASSLOGFILE => $passLogFile,
        ]));
    }

    /**
     * @param bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface $paramValue
     *
     * @throws InvalidArgumentException in case of unsupported builtin param
     *
     * @return self (For static analysis the trick is to return 'self' instead of interface)
     */
    public function withBuiltInParam(string $paramName, $paramValue): VideoConvertParamsInterface
    {
        return new self(array_merge($this->params, [
            $paramName => $paramValue,
        ]));
    }

    /**
     * @return self (For static analysis the trick is to return 'self' instead of interface)
     */
    public function withoutParam(string $paramName): VideoConvertParamsInterface
    {
        $ao = (new \ArrayObject($this->params));
        if ($ao->offsetExists($paramName)) {
            $ao->offsetUnset($paramName);
        }

        return new self($ao->getArrayCopy());
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

    public function isParamValid(string $paramName): bool
    {
        return in_array($paramName, self::BUILTIN_PARAMS, true);
    }

    /**
     * Return a param, throw an exception if the param has not been defined yet or
     * use $default if it was set.
     *
     * @param mixed $default Will return default value instead of throwing exception
     *
     * @return bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface|null
     *
     * @throws UnsetParamException
     */
    public function getParam(string $paramName, $default = null)
    {
        if (!$this->hasParam($paramName)) {
            throw new UnsetParamException(sprintf(
                'Cannot get param \'%s\', it has not been set',
                $paramName
            ));
        }

        return $this->params[$paramName];
    }

    public function hasParam(string $paramName): bool
    {
        return array_key_exists($paramName, $this->params);
    }

    /**
     * @return VideoConvertParams
     */
    public function withConvertParams(VideoConvertParamsInterface $extraParams): VideoConvertParamsInterface
    {
        return new self(
            array_merge($this->toArray(), $extraParams->toArray())
        );
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
                    sprintf('Unsupported built-in param "%s" given.', $paramName)
                );
            }
        }
    }
}
