<?php

declare(strict_types=1);

namespace MediaToolsTest\Recipes;

use MediaToolsTest\TestUtilTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Exception\ProcessConversionException;
use Soluble\MediaTools\VideoConvert;
use Soluble\MediaTools\VideoConvertParams;

class VideoConversionTest extends TestCase
{
    use TestUtilTrait;

    /** @var VideoConvert */
    protected $videoConvert;

    /** @var string */
    protected $baseDir;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->videoConvert = $this->getVideoConvertService();
        $this->baseDir      = dirname(__FILE__, 3);
    }

    public function testBasicUsage(): void
    {
        $inputFile  = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
        $outputFile = "{$this->baseDir}/output/big_buck_bunny_low.output.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('copy')
            ->withOutputFormat('mp4');

        self::assertFileExists($inputFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($inputFile, $outputFile, $convertParams);
        } catch (ProcessConversionException | FileNotFoundException $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);
        unlink($outputFile);
    }

    public function testConversionWithVideoFilter(): void
    {
    }
}
