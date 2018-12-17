<?php

declare(strict_types=1);

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Process\ProcessParams;
use Soluble\MediaTools\Video\Exception\NoOutputGeneratedException;
use Soluble\MediaTools\Video\Exception\ProcessTimedOutException;
use Soluble\MediaTools\Video\Filter\Hqdn3DVideoFilter;
use Soluble\MediaTools\Video\Filter\NlmeansVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoInfoReaderInterface;
use Soluble\MediaTools\Video\VideoThumbGeneratorInterface;
use Soluble\MediaTools\Video\VideoThumbParams;

class VideoThumbGeneratorTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var VideoThumbGeneratorInterface */
    protected $thumbService;

    /** @var VideoInfoReaderInterface */
    protected $infoService;

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
        $this->infoService  = $this->getVideoInfoService();

        $this->baseDir   = dirname(__FILE__, 3);
        $this->outputDir = "{$this->baseDir}/tmp";
        $this->videoFile = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
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
            (new VideoThumbParams())->withTime(0.2)
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
            (new VideoThumbParams())
                ->withSeekTime(new SeekTime(0.25))
                ->withVideoFilter(new VideoFilterChain([
                    new YadifVideoFilter(),
                    new NlmeansVideoFilter(),
                ]))
        );
        self::assertFileExists($outputFile);
        self::assertGreaterThan(0, filesize($outputFile));
        unlink($outputFile);
    }

    public function testThumbWithFrameSelection(): void
    {
        $outputFile = $this->outputDir . '/testThumbWithFrameSelection.jpg';
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $nbFrames = $this->infoService->getInfo($this->videoFile)->getNbFrames();

        $this->thumbService->makeThumbnail(
            $this->videoFile,
            $outputFile,
            (new VideoThumbParams())->withFrame($nbFrames)
        );

        self::assertFileExists($outputFile);
        self::assertGreaterThan(0, filesize($outputFile));
        unlink($outputFile);
    }

    public function testThumbAtEndDurationMustThrowNoOutputGeneratedException(): void
    {
        self::expectException(NoOutputGeneratedException::class);

        $outputFile = $this->outputDir . '/testThumbAtVideoDuration.jpg';
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $duration = $this->infoService->getInfo($this->videoFile)->getDuration();

        $this->thumbService->makeThumbnail(
            $this->videoFile,
            $outputFile,
            (new VideoThumbParams())->withTime(
                $duration
            )
        );
    }

    public function testMakeThumbnailThrowsMissingTimeException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->thumbService->makeThumbnail('/path/path/does_not_exist.mp4', '', new VideoThumbParams());
    }

    public function testMakeThumbnailThrowsFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->thumbService->makeThumbnail('/path/path/does_not_exist.mp4', '', new VideoThumbParams());
    }

    public function testMakeThumbnailMustThrowExceptionOnTimeout(): void
    {
        self::expectException(ProcessTimedOutException::class);

        $outputFile = "{$this->outputDir}/throwExceptionOnTimeout.jpg";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
        $this->thumbService->makeThumbnail(
            $this->videoFile,
            $outputFile,
            (new VideoThumbParams())
                ->withSeekTime(new SeekTime(0.25))
                ->withVideoFilter(new VideoFilterChain([
                    new YadifVideoFilter(),
                    new NlmeansVideoFilter(),
                    new Hqdn3DVideoFilter(),
                ])),
            null,
            new ProcessParams(0.01, null, [])
        );
        self::assertFileExists($outputFile);
    }
}
