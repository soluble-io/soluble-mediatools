<?php

declare(strict_types=1);

namespace MediaToolsTest\Recipes;

use MediaToolsTest\TestUtilTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Exception\ProcessConversionException;
use Soluble\MediaTools\Filter\Video\EmptyVideoFilter;
use Soluble\MediaTools\Filter\Video\VideoFilterChain;
use Soluble\MediaTools\Filter\Video\YadifVideoFilter;
use Soluble\MediaTools\VideoConvert;
use Soluble\MediaTools\VideoConvertParams;

class VideoSimpleConversionTest extends TestCase
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

    public function testFullOptions(): void
    {
        $outputFile = "{$this->outputDir}/testFullOptions.output.webm";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $videoFilterChain = new VideoFilterChain();
        $videoFilterChain->addFilter(new EmptyVideoFilter());
        $videoFilterChain->addFilter(new YadifVideoFilter());


        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('libvpx-vp9')
            //->withCrf(32) - Using variable bitrate instead:
            ->withVideoBitrate('200k') // target bitrate
            ->withVideoMaxBitrate('250000') // max bitrate
            ->withVideoMinBitrate('150k') // min bitrate
            ->withAudioCodec('libopus')
            ->withAudioBitrate('96k')
            ->withVideoFilter($videoFilterChain)
            ->withThreads(0) // 0 means threads = number of cores
            ->withSpeed(8)
            ->withKeyframeSpacing(240)
            ->withTileColumns(1)
            ->withFrameParallel(1)
            ->withPixFmt('yuv420p')
            ->withOutputFormat('webm');

        self::assertFileExists($this->videoFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams);
        } catch (ProcessConversionException | FileNotFoundException $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);
        unlink($outputFile);

        // Check the outputed command
        $process = $this->videoConvert->getConversionProcess($this->videoFile, $outputFile, $convertParams);
        $cmdLine = $process->getCommandLine();

        self::assertContains(' -vcodec libvpx-vp9 ', $cmdLine);
        self::assertContains(' -b:v 200k ', $cmdLine);
        self::assertContains(' -maxrate 250000', $cmdLine);
        self::assertContains(' -minrate 150k ', $cmdLine);
        self::assertContains(' -acodec libopus ', $cmdLine);
        self::assertContains(' -b:a 96k ', $cmdLine);
        self::assertContains(' -vf yadif=mode=0:parity=-1:deint=0 ', $cmdLine);
        self::assertContains(' -threads 0 ', $cmdLine);
        self::assertContains(' -speed 8 ', $cmdLine);
        self::assertContains(' -g 240 ', $cmdLine);
        self::assertContains(' -tile-columns 1 ', $cmdLine);
        self::assertContains(' -frame-parallel 1', $cmdLine);
        self::assertContains(' -pix_fmt yuv420p', $cmdLine);
        self::assertContains(' -f webm ', $cmdLine);

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
            self::assertEquals(1, $e->getCode());
            self::assertEquals(ProcessConversionException::FAILURE_TYPE_PROCESS, $e->getFailureType());
            self::assertContains('Unknown encoder \'NOVALIDCODEC\'', $e->getProcess()->getErrorOutput());
            self::assertContains('Unknown encoder \'NOVALIDCODEC\'', $e->getMessage());
        } catch (\Throwable $e) {
            self::fail(sprintf(
                'Invalid codec must throw a ProcessConversionException! (%s returned)',
                get_class($e)
            ));
        }
    }
}
