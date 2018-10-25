<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Adapter;

interface FFMpegCLIValueInterface
{
    /**
     * Return the value to send to ffmpeg.
     * If null will not be processed by ffmpeg.
     */
    public function getFFmpegCLIValue(): ?string;
}
