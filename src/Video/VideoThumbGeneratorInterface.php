<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Symfony\Component\Process\Process;

interface VideoThumbGeneratorInterface
{
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, ThumbParamsInterface $thumbParams, ?ProcessParamsInterface $processParam = null): Process;

    public function makeThumbnail(string $videoFile, string $thumbnailFile, ThumbParamsInterface $thumbParams, ?callable $callback = null, ?ProcessParamsInterface $processParam = null): void;
}
