<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\PhpUnitPolyfillTrait;
use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\Info\StreamTypeInterface;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\VideoConvertParams;
use Soluble\MediaTools\Video\VideoInfoReaderInterface;

class VideoSubtitleTest extends TestCase
{
    use PhpUnitPolyfillTrait;

    use ServicesProviderTrait;

    /** @var VideoConverterInterface */
    protected $videoConvert;

    /** @var VideoInfoReaderInterface */
    protected $infoService;

    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $outputDir;

    /** @var string */
    protected $subFile;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->videoConvert = $this->getVideoConvertService();
        $this->infoService  = $this->getVideoInfoService();

        $this->baseDir   = dirname(__FILE__, 3);
        $this->outputDir = "{$this->baseDir}/tmp";
        $this->subFile   = "{$this->baseDir}/data/bunny.srt";
    }

    public function testVTTConversion(): void
    {
        $outputFile = "{$this->outputDir}/testBasicUsage.tmp.vtt";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('vtt')
            ->withOverwrite();

        self::assertFileExists($this->subFile);
        self::assertFileDoesNotExistPolyfilled($outputFile);

        try {
            $this->videoConvert->convert($this->subFile, $outputFile, $convertParams);
        } catch (ConverterExceptionInterface $e) {
            self::fail(sprintf('Failed to convert subtitle: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);

        $info = $this->infoService->getInfo($outputFile);
        self::assertEquals('webvtt', $info->getFormatName());
        self::assertEquals(1, $info->countStreams());
        $sub = $info->getSubtitleStreams()->getFirst();
        self::assertEquals(StreamTypeInterface::SUBTITLE, $sub->getCodecType());
        self::assertEquals('webvtt', $sub->getCodecName());
    }
}
