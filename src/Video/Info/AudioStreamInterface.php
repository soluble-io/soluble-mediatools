<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface AudioStreamInterface extends StreamInterface
{
    public function getStartTime(): ?float;

    public function getProfile(): ?string;

    public function getDurationTs(): ?int;

    /**
     * Return stream duration in seconds.
     */
    public function getDuration(): float;

    /**
     * Return tags attached to this stream.
     *
     * @return array<string, string>
     */
    public function getTags(): array;

    /**
     * Return stream bitrate if available (depends on encoder params).
     */
    public function getBitRate(): ?int;
}
