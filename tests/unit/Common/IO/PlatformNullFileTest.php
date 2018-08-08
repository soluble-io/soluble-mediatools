<?php

declare(strict_types=1);

namespace MediaToolsTest\Common\IO;

use PHPUnit\Framework\TestCase;
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
}
