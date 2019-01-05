<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Adapter;

interface FFMpegCLIValueInterface
{
    /**
     * Return the value to send to ffmpeg.
     * If null will not be processed by ffmpeg.
     */
    public function getFFmpegCLIValue(): ?string;
}
