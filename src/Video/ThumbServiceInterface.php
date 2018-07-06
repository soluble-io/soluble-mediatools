<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Video\Filter\VideoFilterInterface;

interface ThumbServiceInterface
{
    public function makeThumbnails(string $videoFile, string $outputFile, float $time = 0.0, ?VideoFilterInterface $videoFilter = null): void;
}
