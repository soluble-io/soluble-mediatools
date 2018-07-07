<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

class IdetVideoFilter implements FFMpegVideoFilterInterface
{
    public function getFFmpegCLIValue(): string
    {
        return 'idet';
    }
}
