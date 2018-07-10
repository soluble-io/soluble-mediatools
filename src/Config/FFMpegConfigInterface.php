<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;

interface FFMpegConfigInterface extends ProcessParamsInterface
{
    public function getBinary(): string;

    /**
     * Return default number of threads ffmpeg will use by default.
     */
    public function getThreads(): ?int;
}
