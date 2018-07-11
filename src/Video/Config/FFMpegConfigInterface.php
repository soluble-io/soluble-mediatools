<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Adapter\AdapterInterface;

interface FFMpegConfigInterface
{
    public function getBinary(): string;

    /**
     * Return default number of threads ffmpeg will use by default.
     */
    public function getThreads(): ?int;

    /**
     * Return underlying ffmpeg/converter adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface;

    /**
     * Return symfony process params (timeout, ideltimeout, env).
     */
    public function getProcessParams(): ProcessParamsInterface;
}
