<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

class EmptyVideoFilter implements VideoFilterInterface
{
    public function getFFMpegCLIArgument(): string
    {
        return '';
    }

    public function getFFmpegCLIValue(): string
    {
        return '';
    }
}
