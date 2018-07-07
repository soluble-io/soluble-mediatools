<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;

interface ThumbServiceInterface
{
    public function makeThumbnail(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null, ?callable $env = null): void;
}
