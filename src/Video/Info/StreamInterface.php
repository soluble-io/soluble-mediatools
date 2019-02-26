<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface StreamInterface
{
    /**
     * Return stream index.
     */
    public function getIndex(): int;

    /**
     * Return the codec type, either video, audio or data.
     */
    public function getCodecType(): string;

    /**
     * Return the codec name: h264, vp8, vp9, h265, av1, opus, aac, mp3...
     */
    public function getCodecName(): string;

    public function getCodecLongName(): ?string;

    public function getCodecTimeBase(): ?string;

    public function getCodecTagString(): ?string;

    public function getTimeBase(): ?string;

    public function getDurationTs(): ?int;

    /**
     * Return stream duration in seconds.
     */
    public function getDuration(): float;

    public function getProfile(): ?string;

    /**
     * Return stream bitrate if available (depends on encoder params).
     */
    public function getBitRate(): ?int;

    /**
     * Return tags attached to this stream.
     *
     * @return array<string, string>
     */
    public function getTags(): array;

    /**
     * Return underlying ffprobe stream metadata.
     *
     * @return array<string, mixed>
     */
    public function getStreamMetadata(): array;
}
