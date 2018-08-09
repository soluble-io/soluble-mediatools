<?php

declare(strict_types=1);

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Exception\MissingInputFileReaderException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\VideoInfoReaderInterface;

class VideoInfoReaderTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var VideoInfoReaderInterface */
    protected $infoService;

    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $videoFile;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->infoService = $this->getVideoInfoService();

        $this->baseDir      = dirname(__FILE__, 3);
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testGetInfo(): void
    {
        $videoInfo = $this->infoService->getInfo($this->videoFile);
        self::assertEquals(61.533000, $videoInfo->getDuration());

        self::assertEquals(realpath($this->videoFile), realpath($videoInfo->getFile()));
        self::assertEquals(1113, $videoInfo->getNbFrames());
        self::assertEquals(320, $videoInfo->getWidth());
        self::assertEquals(180, $videoInfo->getHeight());
        self::assertEquals(['width' => 320, 'height' => 180], $videoInfo->getDimensions());
        self::assertContains('mp4', $videoInfo->getFormatName());
        self::assertEquals(2, $videoInfo->countStreams());

        ['width' => $width, 'height' => $height] = $videoInfo->getDimensions();

        self::assertEquals(320, $width);
        self::assertEquals(180, $height);
    }

    public function testGetMediaInfoThrowsProcessFailedException(): void
    {
        self::expectException(ProcessFailedException::class);
        $this->infoService->getInfo("{$this->baseDir}/data/not_a_video_file.mov");
    }

    public function testGetMediaInfoThrowsMissingInputFileException(): void
    {
        self::expectException(MissingInputFileReaderException::class);
        $this->infoService->getInfo('/path/path/does_not_exist.mp4');
    }

    public function testGetMediaInfoThrowsFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->infoService->getInfo('/path/path/does_not_exist.mp4');
    }
}
