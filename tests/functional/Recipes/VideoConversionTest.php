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

    /** @var string */
    protected $outputDir;

    /** @var string */
    protected $videoFile;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->videoConvert = $this->getVideoConvertService();
        $this->baseDir      = dirname(__FILE__, 3);
        $this->outputDir    = "{$this->baseDir}/output";
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testBasicUsage(): void
    {
        $outputFile = "{$this->outputDir}/testBasicUsage.output.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('copy')
            ->withOutputFormat('mp4');

        self::assertFileExists($this->videoFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams);
        } catch (ProcessConversionException | FileNotFoundException $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);
        unlink($outputFile);
    }

    public function testConvertMustThrowFileNotFoundException(): void
    {
        self::expectException(FileNotFoundException::class);
        $this->videoConvert->convert('/no_exists/test.mov', '/tmp/test.mp4', new VideoConvertParams());
    }

    public function testConvertMustThrowProcessConversionException(): void
    {
        self::expectException(ProcessConversionException::class);

        $outputFile = "{$this->outputDir}/testBasicUsageThrowsProcessConversionException.output.mp4";

        $params = (new VideoConvertParams())->withVideoCodec('NOVALIDCODEC');

        $this->videoConvert->convert($this->videoFile, $outputFile, $params);
    }

    public function testConvertProcessConversionExceptionType(): void
    {
        $outputFile = "{$this->outputDir}/testBasicUsageThrowsProcessConversionException.output.mp4";

        $params = (new VideoConvertParams())->withVideoCodec('NOVALIDCODEC');

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $params);
            self::fail('Video conversion with invalid codec must fail.');
        } catch (ProcessConversionException $e) {
            self::assertTrue($e->wasCausedByProcess());
            var_dump($e->getCode());
            var_dump($e->getMessage());
        } catch (\Throwable $e) {
            self::fail(sprintf(
                'Invalid codec must throw a ProcessConversionException! (%s returned)',
                get_class($e)
            ));
        }
    }
}
