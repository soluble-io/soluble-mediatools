<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Filter\Video;

interface VideoFilterInterface
{
    public function getFFMpegCLIArgument(): string;

    public function getFFmpegCLIValue(): string;
}
