<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Filter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Filter\ScaleFilter;

class ScaleFilterTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testGetFFMpegCLIValue(): void
    {
        self::assertEquals(
            'scale=w=1024:h=800',
            (new ScaleFilter(1024, 800))->getFFmpegCLIValue()
        );

        self::assertEquals(
            'scale=w=1024:h=800:force_original_aspect_ratio=decrease',
            (new ScaleFilter(1024, 800, ScaleFilter::OPTION_ASPECT_RATIO_DECREASE))
            ->getFFmpegCLIValue()
        );

        self::assertEquals(
            'scale=w=1024:h=800:force_original_aspect_ratio=increase',
            (new ScaleFilter(1024, 800, ScaleFilter::OPTION_ASPECT_RATIO_INCREASE))
            ->getFFmpegCLIValue()
        );
    }
}
