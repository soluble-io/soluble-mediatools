<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Filter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;

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
        };

        $filter2 = new class() implements FFMpegVideoFilterInterface {
            public function getFFmpegCLIValue(): string
            {
                return 'filter_2';
            }
        };

        $filter3 = new EmptyVideoFilter();

        $chain = new VideoFilterChain();
        $chain->addFilter($filter1);
        $chain->addFilter($filter2);
        $chain->addFilter($filter3);

        self::assertCount(3, $chain->getFilters());
        $expectedCliValue = '';
        foreach ($chain->getFilters() as $filter) {
            if (!($filter instanceof FFMpegVideoFilterInterface)) {
                continue;
            }

            $expectedCliValue .= $filter->getFFmpegCLIValue();
        }
        self::assertEquals('filter_2', $expectedCliValue);
    }

    public function testFFMpegCliArgumentMustThrowUnsupportedParamValueException(): void
    {
        self::expectException(UnsupportedParamValueException::class);

        $filter1 = new class() implements FFMpegVideoFilterInterface {
            public function getFFmpegCLIValue(): string
            {
                return 'An ffmpeg ready filter'; // empty
            }
        };

        $filter2 = new class() implements VideoFilterInterface {
        };

        $chain = new VideoFilterChain();
        $chain->addFilter($filter1);
        $chain->addFilter($filter2);

        $chain->getFFmpegCLIValue();
    }
}
