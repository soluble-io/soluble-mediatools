<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Assert;

use Soluble\MediaTools\Common\Exception\InvalidArgumentException;

trait BitrateAssertionsTrait
{
    /**
     * Ensure that a bitrate is valid (optional unit: k or M ).
     *
     * @throws InvalidArgumentException
     */
    private function ensureValidBitRateUnit(string $bitrate): void
    {
        if (preg_match('/^\d+(k|M)?$/i', $bitrate) !== 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid bitrate given: "%s" (int(K|M)?)',
                    $bitrate
                )
            );
        }
    }
}
