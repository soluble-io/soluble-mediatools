<?php

declare(strict_types=1);

namespace MediaToolsTest\Common\Assert;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Assert\BitrateAssertionsTrait;
use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\InvalidArgumentException;

class PathAssertionsTraitTest extends TestCase
{
    public function testInvalidFileMustThrowException(): void
    {
        self::expectException(FileNotFoundException::class);

        $cls = new class() {
            use PathAssertionsTrait;

            public function testProtected(string $file): void
            {
                $this->ensureFileExists($file);
            }
        };
        $cls->testProtected('/unexistent/path/file.txt');
    }

    public function testValidBitrateMustWork(): void
    {
        $cls = new class() {
            use PathAssertionsTrait;

            public function testProtected(string $file): void
            {
                $this->ensureFileExists($file);
            }
        };

        $cls->testProtected(__FILE__);
        self::assertTrue(true);
    }
}
