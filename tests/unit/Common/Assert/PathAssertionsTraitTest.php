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
use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\FileNotReadableException;

class PathAssertionsTraitTest extends TestCase
{
    public function testExistingFileMustWork(): void
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

    public function testReadableFileMustWork(): void
    {
        $cls = new class() {
            use PathAssertionsTrait;

            public function testProtected(string $file): void
            {
                $this->ensureFileReadable($file);
            }
        };

        $cls->testProtected(__FILE__);
        self::assertTrue(true);
    }

    public function testEnsureFileReadableMustThrowFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $cls = new class() {
            use PathAssertionsTrait;

            public function testProtected(string $file): void
            {
                $this->ensureFileReadable($file);
            }
        };
        $cls->testProtected('non_existent_file');
    }

    public function testEnsureFileReadableMustThrowFileNotReadableException(): void
    {
        self::expectException(FileNotReadableException::class);
        $file = tempnam(sys_get_temp_dir(), 'mediatools-unit-test');
        if ($file === false) {
            self::markTestSkipped('Cannot create a require temp file');
        }
        chmod($file, 0333);
        $cls = new class() {
            use PathAssertionsTrait;

            public function testProtected(string $file): void
            {
                $this->ensureFileReadable($file);
            }
        };

        try {
            $cls->testProtected($file);
        } catch (\Throwable $e) {
            unlink($file);
            throw $e;
        }
    }
}
