<?php

declare(strict_types=1);

namespace MediaToolsTest\Filter\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\Filter\VideoFilterInterface;

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

    public function testAddFiltersMustReturnCorrectCliArguments(): void
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

        $filter3 = new class() implements VideoFilterInterface {
            public function getFFMpegCLIArgument(): string
            {
                return '-vf';
            }

            public function getFFmpegCLIValue(): string
            {
                return ''; // empty filter
            }
        };

        $chain = new VideoFilterChain();
        $chain->addFilter($filter1);
        $chain->addFilter($filter2);
        $chain->addFilter($filter3);

        self::assertCount(3, $chain->getFilters());
        self::assertEquals('-vf filter_1,filter_2', $chain->getFFMpegCLIArgument());
        self::assertEquals('filter_1,filter_2', $chain->getFFmpegCLIValue());
    }

    public function testEmptyFiltersMustReturnEmptyCLIArg(): void
    {
        $filter1 = new class() implements VideoFilterInterface {
            public function getFFMpegCLIArgument(): string
            {
                return '-vf';
            }

            public function getFFmpegCLIValue(): string
            {
                return ''; // empty
            }
        };

        $filter2 = new class() implements VideoFilterInterface {
            public function getFFMpegCLIArgument(): string
            {
                return '-vf';
            }

            public function getFFmpegCLIValue(): string
            {
                return ''; // empty
            }
        };

        $chain = new VideoFilterChain();
        $chain->addFilter($filter1);
        $chain->addFilter($filter2);

        self::assertCount(2, $chain->getFilters());
        self::assertEquals('', $chain->getFFMpegCLIArgument());
        self::assertEquals('', $chain->getFFmpegCLIValue());
    }
}
