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
use Soluble\MediaTools\Video\Filter\SelectFilter;

class SelectFilterTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testGetFFMpegCLIValue(): void
    {
        self::assertEquals(
            'select=eq(n\,10)',
            (new SelectFilter('eq(n\,10)'))->getFFmpegCLIValue()
        );
    }
}
