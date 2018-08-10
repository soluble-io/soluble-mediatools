<?php

declare(strict_types=1);

namespace MediaToolsTest\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\IOException;
use Soluble\MediaTools\Video\VideoInfo;

class VideoInfoTest extends TestCase
{
    public function setUp(): void
    {
    }

    public static function getExampleFFProbeData(): array
    {
        return [];
    }

    public function testFileMethods(): void
    {
        $vi = new VideoInfo(__FILE__, self::getExampleFFProbeData());

        self::assertSame(__FILE__, $vi->getFile());
        self::assertSame(filesize(__FILE__), $vi->getFileSize());
    }

    public function testFileSizeThrowsIOException(): void
    {
        self::expectException(IOException::class);
        $vi = new VideoInfo('/unexistent_file/path/file', self::getExampleFFProbeData());
        $vi->getFileSize();
    }
}
