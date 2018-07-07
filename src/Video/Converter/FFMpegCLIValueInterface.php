<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Converter;

interface FFMpegCLIValueInterface
{
    /**
     * Return the value to send to ffmpeg.
     *
     * @return string
     */
    public function getFFmpegCLIValue(): string;
}
