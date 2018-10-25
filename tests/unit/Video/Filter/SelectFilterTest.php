<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Filter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Filter\SelectFilter;

class SelectFilterTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testGetFFMpegCLIValue(): void
    {
        self::assertEquals(
            '"select=eq(n\,10)"',
            (new SelectFilter('eq(n\,10)'))->getFFmpegCLIValue()
        );

    }
}
