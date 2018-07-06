<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

class NlmeansVideoFilter extends AbstractVideoFilter implements VideoFilterTypeDenoiseInterface
{
    public function getFFmpegCLIValue(): string
    {
        return 'nlmeans';
    }
}
