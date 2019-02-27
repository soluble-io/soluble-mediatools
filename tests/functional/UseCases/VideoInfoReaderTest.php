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
use Symfony\Component\Cache\Simple\ArrayCache;

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

    public function testGetInfoWithCache(): void
    {
        $cache = new ArrayCache();

        $videoInfo = $this->infoService->getInfo($this->videoFile, $cache);

        $cacheKey = array_keys($cache->getValues())[0] ?? 'nothing_in_cache';
        self::assertTrue($cache->has($cacheKey));

        $cachedVideoInfo = $this->infoService->getInfo($this->videoFile, $cache);

        self::assertEquals($videoInfo->getDuration(), $cachedVideoInfo->getDuration());

        $cached = json_decode($cache->get($cacheKey), true);
        self::assertEquals($videoInfo->getDuration(), $cached['format']['duration']);

        // modified cache
        $cached['format']['duration'] = 10;
        $newEntry                     = json_encode($cached);

        $cache->set($cacheKey, $newEntry);

        $cachedVideoInfo2 = $this->infoService->getInfo($this->videoFile, $cache);

        self::assertEquals(10, $cachedVideoInfo2->getDuration());
    }

    public function testGetCorruptedCache(): void
    {
        $cache     = new ArrayCache();
        $videoInfo = $this->infoService->getInfo($this->videoFile, $cache);

        $cacheKey = array_keys($cache->getValues())[0] ?? 'nothing_in_cache';
        $cache->set($cacheKey, 'corrupted data');

        $cachedVideoInfo = $this->infoService->getInfo($this->videoFile, $cache);

        self::assertEquals($videoInfo->getDuration(), $cachedVideoInfo->getDuration());
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
