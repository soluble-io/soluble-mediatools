<?php

declare(strict_types=1);

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\InfoServiceInterface;

class VideoInfoTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var InfoServiceInterface */
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
    }

    public function testGetMEdiaInfoThrowsFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->infoService->getInfo('/path/path/does_not_exist.mp4');
    }
}
