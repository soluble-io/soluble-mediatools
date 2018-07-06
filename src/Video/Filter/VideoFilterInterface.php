<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

interface VideoFilterInterface
{
    public function getFFMpegCLIArgument(): string;

    public function getFFmpegCLIValue(): string;
}
