<?php

declare(strict_types=1);

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Filter\Hqdn3DVideoFilter;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\ThumbServiceInterface;

class VideoThumbnailingTest extends TestCase
{
    use ServicesProviderTrait;

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
        $this->outputDir    = "{$this->baseDir}/tmp";
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testSimpleThumbnail(): void
    {
        $outputFile = $this->outputDir . '/testSimpleThumbnail.jpg';
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
        $this->thumbService->makeThumbnail(
            $this->videoFile,
            $outputFile,
            new SeekTime(0.2)
        );
        self::assertFileExists($outputFile);
        self::assertGreaterThan(0, filesize($outputFile));
        unlink($outputFile);
    }

    public function testThumbnailWithFilter(): void
    {
        $outputFile = $this->outputDir . '/testThumbnailWithFilter.jpg';
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
        $this->thumbService->makeThumbnail(
            $this->videoFile,
            $outputFile,
            new SeekTime(0.2),
            new Hqdn3DVideoFilter()
        );
        self::assertFileExists($outputFile);
        self::assertGreaterThan(0, filesize($outputFile));
        unlink($outputFile);
    }

    public function testMakeThumbnailThrowsFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->thumbService->makeThumbnail('/path/path/does_not_exist.mp4', '');
    }
}
