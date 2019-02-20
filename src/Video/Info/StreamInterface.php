<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface StreamInterface
{
    public function getIndex(): int;

    public function getCodecType(): string;

    public function getCodecName(): string;

    public function getCodecLongName(): ?string;

    public function getCodecTimeBase(): ?string;

    public function getCodecTagString(): ?string;

    public function getTimeBase(): ?string;

    public function getDurationTs(): ?int;

    public function getDuration(): float;

    public function getProfile(): ?string;

    public function getBitRate(): ?int;

    /**
     * @return array<string, string>
     */
    public function getTags(): array;

    /**
     * @return array<string, mixed>
     */
    public function getStreamMetadata(): array;
}
