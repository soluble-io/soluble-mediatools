<?php

declare(strict_types=1);

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\ProcessExceptionInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\ConversionParams;
use Soluble\MediaTools\Video\Exception\ConversionExceptionInterface;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessTimedOutException;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;
use Soluble\MediaTools\Video\ProcessParams;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoConverter as VideoConversionService;
use Soluble\MediaTools\Video\VideoConverterInterface;

class VideoConverterTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var VideoConverterInterface */
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
        $this->outputDir    = "{$this->baseDir}/tmp";
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testBasicConversion(): void
    {
        $outputFile = "{$this->outputDir}/testBasicUsage.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new ConversionParams())
            ->withVideoCodec('libx264')
            ->withPreset('ultrafast')
            ->withTune('animation')
            ->withOverwrite()
            // Will speed up test
            ->withSeekStart(new SeekTime(1))
            ->withSeekEnd(new SeekTime(1.2))
            ->withCrf(20);

        self::assertFileExists($this->videoFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams);
        } catch (ConversionExceptionInterface $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);
        unlink($outputFile);
    }

    public function testWithMoreOptions(): void
    {
        $outputFile = "{$this->outputDir}/testFullOptions.tmp.webm";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new ConversionParams())
            ->withVideoCodec('libvpx-vp9')
            //->withCrf(32) - Using variable bitrate instead:
            ->withSeekStart(new SeekTime(1))
            ->withVideoBitrate('200k') // target bitrate
            ->withVideoMaxBitrate('250000') // max bitrate
            ->withVideoMinBitrate('150k') // min bitrate
            ->withAudioCodec('libopus')
            ->withAudioBitrate('96k')
            ->withVideoFilter(new YadifVideoFilter())
            ->withThreads(0) // 0 means threads = number of cores
            ->withSpeed(8)
            ->withKeyframeSpacing(240)
            ->withTileColumns(1)
            ->withFrameParallel(1)
            ->withPixFmt('yuv420p')
            // Will speed up - takes only 25 frames
            ->withVideoFrames(25)
            ->withOutputFormat('webm');

        self::assertFileExists($this->videoFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams);
        } catch (ConversionExceptionInterface $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);
        unlink($outputFile);
    }

    public function testConvertMustThrowFileNotFoundException(): void
    {
        self::expectException(MissingInputFileException::class);
        $this->videoConvert->convert('/no_exists/test.mov', '/tmp/test.mp4', new ConversionParams());
    }

    public function testConvertInvalidCodecMustThrowProcessException(): void
    {
        self::expectException(ProcessExceptionInterface::class);

        $outputFile = "{$this->outputDir}/testBasicUsageThrowsProcessConversionException.tmp.mp4";

        $params = (new ConversionParams())->withVideoCodec('NOVALIDCODEC');

        $this->videoConvert->convert($this->videoFile, $outputFile, $params);
    }

    public function testConvertInvalidFileMustThrowProcessException(): void
    {
        self::expectException(ProcessExceptionInterface::class);

        $outputFile = "{$this->outputDir}/testBasicUsageThrowsProcessConversionException.tmp.mp4";

        $params = (new ConversionParams())->withVideoCodec('NOVALIDCODEC');

        $this->videoConvert->convert("{$this->baseDir}/data/not_a_video_file.mov", $outputFile, $params);
    }

    public function testConvertMustThrowExceptionOnDefaultTimeout(): void
    {
        self::expectException(ProcessTimedOutException::class);

        $outputFile = "{$this->outputDir}/throwExceptionOnGlobalTimeout.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new ConversionParams())
            ->withVideoCodec('h264');

        $container    = $this->getConfiguredContainer();
        $globalConfig = $container->get(FFMpegConfigInterface::class);

        $ffmpegConfig = new FFMpegConfig($globalConfig->getBinary(), null, $timeout = 0.2);
        $videoConvert = new VideoConversionService($ffmpegConfig);

        $videoConvert->convert($this->videoFile, $outputFile, $convertParams);
    }

    public function testConvertMustThrowExceptionOnTimeout(): void
    {
        self::expectException(ProcessTimedOutException::class);

        $outputFile = "{$this->outputDir}/throwExceptionOnTimeout.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new ConversionParams())
            ->withVideoCodec('h264');

        $processParams = new ProcessParams(0.1, null, []);

        $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams, null, $processParams);
    }

    public function testConvertMustThrowExceptionOnIdleTimeout(): void
    {
        self::expectException(ProcessTimedOutException::class);

        $outputFile = "{$this->outputDir}/throwExceptionOnTimeout.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new ConversionParams())
            ->withVideoCodec('h264');

        $processParams = new ProcessParams(null, 0.1, []);

        $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams, null, $processParams);
    }
}
