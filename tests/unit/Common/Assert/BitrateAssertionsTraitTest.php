<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Common\Assert;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Assert\BitrateAssertionsTrait;
use Soluble\MediaTools\Common\Exception\InvalidArgumentException;

class BitrateAssertionsTraitTest extends TestCase
{
    public function testInvalidBitrateMustThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cls = new class() {
            use BitrateAssertionsTrait;

            public function testProtected(string $bitrate): void
            {
                $this->ensureValidBitRateUnit($bitrate);
            }
        };
        $cls->testProtected('2F');
    }

    public function testValidBitrateMustWork(): void
    {
        $cls = new class() {
            use BitrateAssertionsTrait;

            public function testProtected(string $bitrate): void
            {
                $this->ensureValidBitRateUnit($bitrate);
            }
        };
        $cls->testProtected('2k');
        self::assertTrue(true);
    }
}
