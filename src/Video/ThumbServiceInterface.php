<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Symfony\Component\Process\Process;

interface ThumbServiceInterface
{
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null): Process;

    public function makeThumbnail(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null, ?callable $env = null): void;
}
