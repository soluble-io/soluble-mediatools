<?php

declare(strict_types=1);

namespace MediaToolsTest\Recipes;

use MediaToolsTest\TestUtilTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Detection\InterlaceGuess;
use Soluble\MediaTools\Video\ThumbServiceInterface;

class VideoThumbnailingTest extends TestCase
{
    use TestUtilTrait;

    /** @var ThumbServiceInterface */
    protected $thumbService;

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
        $this->thumbService = $this->getVideoThumbService();

        $this->baseDir      = dirname(__FILE__, 3);
        $this->outputDir    = "{$this->baseDir}/output";
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testMakeThumbnail(): void
    {
        $this->thumbService->makeThumbnails(
                $this->videoFile,
                $this->outputDir . '/thumb.jpg',
                0.2
        );

    }

}
