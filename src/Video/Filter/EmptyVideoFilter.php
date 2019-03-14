<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

final class EmptyVideoFilter implements FFMpegVideoFilterInterface
{
    public function getFFmpegCLIValue(): string
    {
        return '';
    }
}
