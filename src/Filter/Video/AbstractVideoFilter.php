<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Filter\Video;

abstract class AbstractVideoFilter implements VideoFilterInterface
{
    public function getFFMpegCLIArgument(): string
    {
        if (trim($this->getFFmpegCLIValue()) === '') {
            return '';
        }

        return '-vf ' . $this->getFFmpegCLIValue();
    }
}
