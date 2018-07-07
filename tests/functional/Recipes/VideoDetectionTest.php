<?php

declare(strict_types=1);

namespace MediaToolsTest\Recipes;

use MediaToolsTest\TestUtilTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Detection\InterlaceDetectGuess;
use Soluble\MediaTools\Video\DetectionServiceInterface;

class VideoDetectionTest extends TestCase
{
    use TestUtilTrait;

    /** @var DetectionServiceInterface */
    protected $detectionService;

    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $outputDir;

    /** @var string */
    protected $videoFile;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->detectionService = $this->getVideoDetectionService();

        $this->baseDir      = dirname(__FILE__, 3);
        $this->outputDir    = "{$this->baseDir}/output";
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testDetectInterlacement(): void
    {
        $interlaceGuess = $this->detectionService
            ->detectInterlacement($this->videoFile, 100);

        self::assertFalse($interlaceGuess->isInterlaced(0.1));
        self::assertFalse($interlaceGuess->isInterlacedTff(0.1));
        self::assertFalse($interlaceGuess->isInterlacedBff(0.1));
        self::assertEquals(InterlaceDetectGuess::MODE_PROGRESSIVE, $interlaceGuess->getBestGuess());

        $stats = $interlaceGuess->getStats();
        self::assertGreaterThan(0.8, $stats[InterlaceDetectGuess::MODE_PROGRESSIVE]);
        self::assertLessThan(0.1, $stats[InterlaceDetectGuess::MODE_UNDETERMINED]);
        self::assertLessThan(0.05, $stats[InterlaceDetectGuess::MODE_INTERLACED_TFF]);

        // Because some frames where detected as interlaced ttf > 0.01 %
        self::assertTrue($interlaceGuess->isInterlacedTff(0.01));
    }

    public function testDetectInterlacementThrowsFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->detectionService->detectInterlacement('/path/path/does_not_exist.mp4');
    }
}
