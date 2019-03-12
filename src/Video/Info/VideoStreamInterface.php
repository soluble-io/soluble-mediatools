<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface VideoStreamInterface extends StreamInterface
{
    public function getCodecTimeBase(): ?string;

    public function getStartTime(): ?float;

    public function getDurationTs(): ?int;

    /**
     * Return stream duration in seconds.
     */
    public function getDuration(): float;

    public function getCodecTagString(): ?string;

    public function getProfile(): ?string;

    public function getWidth(): int;

    public function getHeight(): int;

    public function getCodedWidth(): ?int;

    public function getCodedHeight(): ?int;

    /**
     * Return (display) aspect ratio object.
     *
     * @see VideoStreamInterface::getDisplayAspectRatio() for the ffprobe version
     */
    public function getAspectRatio(): ?AspectRatio;

    public function getSampleAspectRatio(): ?string;

    public function getDisplayAspectRatio(): ?string;

    public function getPixFmt(): ?string;

    public function getLevel(): ?int;

    /**
     * What ffprobe returns, i.e:'1484/81', you don't generally want this.
     *
     * @see self::getRFrameRate()
     * @see self::getFps()
     */
    public function getAvgFrameRate(): ?string;

    /**
     * What ffprobe returns, i.e:'24000/1001', '25/1', '24000/1001'.
     *
     * @see self::getFps()
     */
    public function getRFrameRate(): ?string;

    public function getNbFrames(): ?int;

    public function isAvc(): ?bool;

    public function getColorRange(): ?string;

    public function getColorSpace(): ?string;

    public function getColorTransfer(): ?string;

    /**
     * Convenience method to return the number of frames per second.
     *
     * The FPS will generally be computed from the self::getRFrameRate(),
     * if the info is not present, it will attempt to compute the fps
     * from duration and total number of frames. If none works, it returns
     * null.
     *
     * @see self::getAvgFrameRate()
     * @see self::getRFrameRate()
     * @see self::getDuration()
     * @see self::getNbFrames()
     *
     * @param int|null $decimals fps can be rounded to the number of decimals, null means no rounding
     */
    public function getFps(?int $decimals = null): ?float;

    /**
     * Return stream bitrate if available (depends on encoder params).
     */
    public function getBitRate(): ?int;

    /**
     * Return associative array with width and height.
     *
     * @return array<string, int>
     */
    public function getDimensions(): array;

    /**
     * Return tags attached to this stream.
     *
     * @return array<string, string>
     */
    public function getTags(): array;
}
