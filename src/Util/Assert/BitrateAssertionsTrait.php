<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Util\Assert;

use Soluble\MediaTools\Exception\InvalidArgumentException;

trait BitrateAssertionsTrait
{
    /**
     * Ensure that a bitrate is valid (optional unit: k or M ).
     *
     * @throws InvalidArgumentException
     */
    protected function ensureValidBitRateUnit(string $bitrate): void
    {
        if (preg_match('/^\d+(k|M)?$/i', $bitrate) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s"', $bitrate));
        }
    }
}
