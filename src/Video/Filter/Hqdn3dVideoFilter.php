<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

class Hqdn3dVideoFilter extends AbstractVideoFilter implements VideoFilterTypeDenoiseInterface
{
    public function getFFmpegCLIValue(): string
    {
        return 'hqdn3d';
    }
}
