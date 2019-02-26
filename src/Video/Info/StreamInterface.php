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

    /**
     * Return underlying ffprobe stream metadata.
     *
     * @return array<string, mixed>
     */
    public function getStreamMetadata(): array;
}
