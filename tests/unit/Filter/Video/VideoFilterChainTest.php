<?php

declare(strict_types=1);

namespace MediaToolsTest\Filter\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Filter\Video\EmptyVideoFilter;
use Soluble\MediaTools\Filter\Video\VideoFilterChain;
use Soluble\MediaTools\Filter\Video\VideoFilterInterface;

class VideoFilterChainTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testAddFilterMustReturnAddedFilter(): void
    {
        $emptyFilter = new EmptyVideoFilter();
        $chain       = new VideoFilterChain();
        $chain->addFilter($emptyFilter);
        self::assertSame($emptyFilter, $chain->getFilters()[0]);
    }

    public function testGetFFmpegCliArguments(): void
    {
        $filter1 = new class() implements VideoFilterInterface {
            public function getFFMpegCLIArgument(): string
            {
                return '-vf';
            }

            public function getFFmpegCLIValue(): string
            {
                return 'filter_1';
            }
        };

        $filter2 = new class() implements VideoFilterInterface {
            public function getFFMpegCLIArgument(): string
            {
                return '-vf';
            }

            public function getFFmpegCLIValue(): string
            {
                return 'filter_2';
            }
        };

        $chain = new VideoFilterChain();
        $chain->addFilter($filter1);
        $chain->addFilter($filter2);

        self::assertCount(2, $chain->getFilters());
        self::assertEquals('-vf filter_1,filter_2', $chain->getFFMpegCLIArgument());
        self::assertEquals('filter_1,filter_2', $chain->getFFmpegCLIValue());
    }
}
