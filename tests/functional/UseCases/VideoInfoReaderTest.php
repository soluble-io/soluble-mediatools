<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Exception\MissingFFProbeBinaryException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
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

        $this->baseDir   = dirname(__FILE__, 3);
        $this->videoFile = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testGetInfo(): void
    {
        $videoInfo = $this->infoService->getInfo($this->videoFile);

        self::assertEquals(61.533000, $videoInfo->getDuration());

        self::assertEquals(realpath($this->videoFile), realpath($videoInfo->getFile()));
        self::assertEquals(1113, $videoInfo->getVideoStreams()->getFirst()->getNbFrames());
        self::assertEquals(320, $videoInfo->getVideoStreams()->getFirst()->getWidth());
        self::assertEquals(180, $videoInfo->getVideoStreams()->getFirst()->getHeight());
        self::assertEquals(['width' => 320, 'height' => 180], $videoInfo->getVideoStreams()->getFirst()->getDimensions());
        self::assertContains('mp4', $videoInfo->getFormatName());
        self::assertEquals(2, $videoInfo->countStreams());

        ['width' => $width, 'height' => $height] = $videoInfo->getVideoStreams()->getFirst()->getDimensions();

        self::assertEquals(320, $width);
        self::assertEquals(180, $height);
    }

    public function testMissingFFProbeBinary(): void
    {
        self::expectException(MissingFFProbeBinaryException::class);
        $infoService = $this->getConfiguredContainer(false, './path/ffmpeg', './path/ffprobe')
            ->get(VideoInfoReaderInterface::class);
        $infoService->getInfo($this->videoFile);
    }

    public function testGetMediaInfoThrowsProcessFailedException(): void
    {
        self::expectException(ProcessFailedException::class);
        $this->infoService->getInfo("{$this->baseDir}/data/not_a_video_file.mov");
    }

    public function testGetMediaInfoThrowsMissingInputFileException(): void
    {
        self::expectException(MissingInputFileException::class);
        $this->infoService->getInfo('/path/path/does_not_exist.mp4');
    }

    public function testGetMediaInfoThrowsFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->infoService->getInfo('/path/path/does_not_exist.mp4');
    }
}
