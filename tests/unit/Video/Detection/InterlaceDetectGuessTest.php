<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Detection;

use MediaToolsTest\Util\FFProbeMetadataProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Detection\InterlaceDetectGuess;

class InterlaceDetectGuessTest extends TestCase
{
    use FFProbeMetadataProviderTrait;

    public function setUp(): void
    {
    }

    public function testGuessWithTTF(): void
    {
        $g = new InterlaceDetectGuess(10, 3, 2, 1);

        self::assertEquals([
            InterlaceDetectGuess::MODE_INTERLACED_TFF => 10 / 16,
            InterlaceDetectGuess::MODE_INTERLACED_BFF => 3 / 16,
            InterlaceDetectGuess::MODE_PROGRESSIVE    => 2 / 16,
            InterlaceDetectGuess::MODE_UNDETERMINED   => 1 / 16,
            ], $g->getStats());

        self::assertTrue($g->isInterlacedTff());
        self::assertFalse($g->isInterlacedBff());
        self::assertFalse($g->isProgressive());
        self::assertFalse($g->isUndetermined());

        self::assertTrue($g->isInterlacedTff(0.5));
        self::assertFalse($g->isInterlacedBff(0.5));
        self::assertFalse($g->isProgressive(0.5));
        self::assertFalse($g->isUndetermined(0.5));

        self::assertTrue($g->isUndetermined(0.000001));

        self::assertEquals(InterlaceDetectGuess::MODE_INTERLACED_TFF, $g->getBestGuess());
        self::assertEquals(InterlaceDetectGuess::MODE_INTERLACED_TFF, $g->getBestGuess(0.5));

        self::assertEquals(InterlaceDetectGuess::MODE_UNDETERMINED, $g->getBestGuess(0.999));
    }

    public function testGuessWithBFF(): void
    {
        $g = new InterlaceDetectGuess(3, 10, 2, 1);

        self::assertEquals([
            InterlaceDetectGuess::MODE_INTERLACED_TFF => 3 / 16,
            InterlaceDetectGuess::MODE_INTERLACED_BFF => 10 / 16,
            InterlaceDetectGuess::MODE_PROGRESSIVE    => 2 / 16,
            InterlaceDetectGuess::MODE_UNDETERMINED   => 1 / 16,
        ], $g->getStats());

        self::assertTrue($g->isInterlacedBff());
        self::assertFalse($g->isInterlacedTff());
        self::assertFalse($g->isProgressive());
        self::assertFalse($g->isUndetermined());

        self::assertTrue($g->isInterlacedBff(0.5));
        self::assertFalse($g->isInterlacedTff(0.5));
        self::assertFalse($g->isProgressive(0.5));
        self::assertFalse($g->isUndetermined(0.5));

        self::assertTrue($g->isUndetermined(0.000001));

        self::assertEquals(InterlaceDetectGuess::MODE_INTERLACED_BFF, $g->getBestGuess());
        self::assertEquals(InterlaceDetectGuess::MODE_INTERLACED_BFF, $g->getBestGuess(0.5));

        self::assertEquals(InterlaceDetectGuess::MODE_UNDETERMINED, $g->getBestGuess(0.999));
    }

    public function testGetDeinterlaceVideoFilter(): void
    {
        $g      = new InterlaceDetectGuess(3, 10, 2, 1);
        $filter = $g->getDeinterlaceVideoFilter();
        self::assertEquals('yadif=mode=0:parity=1:deint=0', $filter->getFFmpegCLIValue());

        $g      = new InterlaceDetectGuess(10, 3, 2, 1);
        $filter = $g->getDeinterlaceVideoFilter();
        self::assertEquals('yadif=mode=0:parity=0:deint=0', $filter->getFFmpegCLIValue());

        $filter = $g->getDeinterlaceVideoFilter(0.999);
        self::assertEquals('', $filter->getFFmpegCLIValue());
    }
}
