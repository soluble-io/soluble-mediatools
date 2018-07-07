<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Filter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;

class EmptyVideoFilterTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testGetFFMpegCLIValueMustReturnEmptyString(): void
    {
        $emptyFilter = new EmptyVideoFilter();
        self::assertEquals('', $emptyFilter->getFFmpegCLIValue());
    }
}
