<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Adapter\ConverterAdapterInterface;

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
     * @return ConverterAdapterInterface
     */
    public function getAdapter(): ConverterAdapterInterface;

    /**
     * Return symfony process params (timeout, ideltimeout, env).
     */
    public function getProcessParams(): ProcessParamsInterface;
}
