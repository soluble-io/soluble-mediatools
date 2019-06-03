<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Common\Assert;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Assert\BinaryAssertionsTrait;
use Soluble\MediaTools\Common\Exception\MissingBinaryException;

class BinaryAssertionsTraitTest extends TestCase
{
    public function testBinaryAvailable(): void
    {
        if (!file_exists('/bin/ls')) {
            self::markTestSkipped('This test can only be run on linux/unix platforms');

            return;
        }

        $cls = new class() {
            use BinaryAssertionsTrait;

            public function testProtected(string $binary): void
            {
                $this->ensureBinaryAvailable($binary);
            }
        };

        $cls->testProtected('/bin/ls');
        self::assertTrue(true);
    }

    public function testBinaryInPathIsConsideredAvailable(): void
    {
        $cls = new class() {
            use BinaryAssertionsTrait;

            public function testProtected(string $binary): void
            {
                $this->ensureBinaryAvailable($binary);
            }
        };
        $cls->testProtected('ls');
        self::assertTrue(true);
    }

    public function testMissingBinaryException(): void
    {
        $this->expectException(MissingBinaryException::class);

        $cls = new class() {
            use BinaryAssertionsTrait;

            public function testProtected(string $binary): void
            {
                $this->ensureBinaryAvailable($binary);
            }
        };
        $cls->testProtected('/path/unexistent/test');
    }
}
