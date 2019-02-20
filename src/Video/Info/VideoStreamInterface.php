<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;


interface VideoStreamInterface
{

    public function getIndex(): int;

    public function getCodecName(): string;

    public function getCodecLongName(): ?string;

    public function getCodecTimeBase(): ?string;

    public function getCodecTagString(): ?string;

    public function getWidth(): int;

    public function getHeight(): int;

    public function getCodedWidth(): ?int;

    public function getCodedHeight(): ?int;

    public function getSampleAspectRatio(): ?string;

    public function getDisplayAspectRatio(): string;

    public function getPixFmt(): ?string;

    public function getAvgFrameRate(): string;

    public function getRFrameRate(): ?string;

    public function getTimeBase(): ?string;

    public function getDurationTs(): ?int;

    public function getDuration(): float;

    public function getProfile(): ?string;

    public function getBitRate(): ?int;

    public function getNbFrames(): int;

    public function isAvc(): ?bool;

    public function getLevel(): ?int;

    public function getColorRange(): ?string;

    public function getColorSpace(): ?string;

    public function getColorTransfer(): ?string;

    /**
     * @return array<string, string>
     */
    public function getTags(): array;
}
