<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Symfony\Component\Process\Process;

interface VideoThumbGeneratorInterface
{
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, VideoThumbParamsInterface $thumbParams, ?ProcessParamsInterface $processParams = null): Process;

    public function makeThumbnail(string $videoFile, string $thumbnailFile, VideoThumbParamsInterface $thumbParams, ?callable $callback = null, ?ProcessParamsInterface $processParams = null): void;
}
