<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Filter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
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

    public function testCountable(): void
    {
        $chain = new VideoFilterChain();
        self::assertEquals(0, $chain->count());
        $chain->addFilter(new EmptyVideoFilter());
        self::assertEquals(1, $chain->count());
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
        $this->expectException(UnsupportedParamValueException::class);

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

    public function testContructorFilters(): void
    {
        $filters = [
            new EmptyVideoFilter(),
            new EmptyVideoFilter(),
        ];
        $chain = new VideoFilterChain($filters);
        self::assertSame($filters, $chain->getFilters());
    }

    public function testAddFilters(): void
    {
        $filters = [
            new EmptyVideoFilter(),
            new EmptyVideoFilter(),
        ];
        $chain = new VideoFilterChain();
        $chain->addFilters($filters);
        self::assertSame($filters, $chain->getFilters());
    }

    public function testAddEmptyChainToChain(): void
    {
        $filters = [
            new VideoFilterChain(),
        ];
        $chain = new VideoFilterChain();
        $chain->addFilters($filters);
        self::assertEquals(0, $chain->count());
    }

    public function testAddFiltersThrowsInvalidArgumentExceptionWithScalar(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $filters = [
            new EmptyVideoFilter(),
            'cool',
        ];
        $chain = new VideoFilterChain();
        $chain->addFilters($filters);
    }

    public function testAddFiltersThrowsInvalidArgumentExceptionWithObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $filters = [
            new EmptyVideoFilter(),
            new \stdClass(),
        ];
        $chain = new VideoFilterChain();
        $chain->addFilters($filters);
    }
}
