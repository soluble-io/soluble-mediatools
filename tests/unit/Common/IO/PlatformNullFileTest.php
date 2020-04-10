<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Common\IO;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\InvalidArgumentException;
use Soluble\MediaTools\Common\IO\PlatformNullFile;

class PlatformNullFileTest extends TestCase
{
    public function testNullFile(): void
    {
        $win = new PlatformNullFile(PlatformNullFile::PLATFORM_WIN);
        self::assertEquals('NUL', $win->getNullFile());
        $linux = new PlatformNullFile(PlatformNullFile::PLATFORM_LINUX);
        self::assertEquals('/dev/null', $linux->getNullFile());
    }

    public function testInvalidPlatformThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PlatformNullFile('BEOS');
    }
}
