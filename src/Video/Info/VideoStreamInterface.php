<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface VideoStreamInterface extends StreamInterface
{
    public function getCodecTimeBase(): ?string;

    public function getCodecTagString(): ?string;

    public function getWidth(): int;

    public function getHeight(): int;

    public function getCodedWidth(): ?int;

    public function getCodedHeight(): ?int;

    public function getSampleAspectRatio(): ?string;

    public function getDisplayAspectRatio(): ?string;

    public function getPixFmt(): ?string;

    public function getLevel(): ?int;

    public function getAvgFrameRate(): ?string;

    public function getRFrameRate(): ?string;

    public function getNbFrames(): ?int;

    public function isAvc(): ?bool;

    public function getColorRange(): ?string;

    public function getColorSpace(): ?string;

    public function getColorTransfer(): ?string;

    /**
     * Return associative array with width and height.
     *
     * @return array<string, int>
     */
    public function getDimensions(): array;
}
