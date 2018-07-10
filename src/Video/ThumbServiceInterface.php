<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Symfony\Component\Process\Process;

interface ThumbServiceInterface
{
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null, ?ProcessParamsInterface $processParam = null): Process;

    public function makeThumbnail(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null, ?callable $callback = null, ?ProcessParamsInterface $processParam = null): void;
}
