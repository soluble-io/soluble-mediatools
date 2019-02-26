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
use Soluble\MediaTools\Common\Exception\ProcessExceptionInterface;
use Soluble\MediaTools\Common\IO\PlatformNullFile;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\Exception\MissingFFMpegBinaryException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\ProcessTimedOutException;
use Soluble\MediaTools\Video\Filter\ScaleFilter;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;
use Soluble\MediaTools\Video\Info\StreamTypeInterface;
use Soluble\MediaTools\Video\Process\ProcessParams;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoConverter as VideoConversionService;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\VideoConvertParams;
use Soluble\MediaTools\Video\VideoConvertParamsInterface;
use Soluble\MediaTools\Video\VideoInfoReaderInterface;

class VideoConverterTest extends TestCase
{
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
    protected $videoFile;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->videoConvert = $this->getVideoConvertService();
        $this->infoService  = $this->getVideoInfoService();

        $this->baseDir   = dirname(__FILE__, 3);
        $this->outputDir = "{$this->baseDir}/tmp";
        $this->videoFile = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testMissingFFMpegBinary(): void
    {
        self::expectException(MissingFFMpegBinaryException::class);
        $convert = $this->getConfiguredContainer(false, './path/ffmpeg', './path/ffprobe')
            ->get(VideoConverterInterface::class);

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('libx264')
            ->withPreset('ultrafast')
            ->withOverwrite()
            // Will speed up test
            ->withSeekStart(new SeekTime(1))
            ->withSeekEnd(new SeekTime(1.2));

        $convert->convert($this->videoFile, '/tmp/a.tmp.mp4', $convertParams);
    }

    public function testBasicConversion(): void
    {
        $outputFile = "{$this->outputDir}/testBasicUsage.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('libx264')
            ->withPreset('ultrafast')
            ->withTune('animation')
            ->withOverwrite()
            // Will speed up test
            ->withSeekStart(new SeekTime(1))
            ->withSeekEnd(new SeekTime(1.2))
            ->withVideoFilter(new ScaleFilter(
                320,
                200,
                ScaleFilter::OPTION_ASPECT_RATIO_DECREASE
            ))
            ->withCrf(20);

        self::assertFileExists($this->videoFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams);
        } catch (ConverterExceptionInterface $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);

        $info   = $this->infoService->getInfo($outputFile);
        $stream = $info->getVideoStreams()->getFirst();
        self::assertEquals(320, $stream->getWidth());
        // Because of ratio decrease !
        self::assertEquals(180, $stream->getHeight());

        unlink($outputFile);
    }

    public function testScaling(): void
    {
        $outputFile = "{$this->outputDir}/testScaling.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('vp9')
            ->withVideoBitrate('50k')
            ->withOverwrite()
            // Will speed up test
            ->withSeekStart(new SeekTime(1))
            ->withSeekEnd(new SeekTime(1.2))
            ->withVideoFilter(new ScaleFilter(
                'iw*.5',
                'ih*.5'
            ))
            ->withCrf(20);

        try {
            $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams);
        } catch (ConverterExceptionInterface $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);

        $info   = $this->infoService->getInfo($outputFile);
        $stream = $info->getVideoStreams()->getFirst();
        self::assertEquals(160, $stream->getWidth());
        // Because of ratio decrease !
        self::assertEquals(90, $stream->getHeight());
        self::assertLessThan(50000, $stream->getBitRate());
        unlink($outputFile);
    }

    public function testConvertVP9SinglePass(): void
    {
        $outputFile = "{$this->outputDir}/testConvertVP9SinglePass.webm";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
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
        } catch (ConverterExceptionInterface $e) {
            self::fail(sprintf('Failed to convert video: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);

        $info = $this->infoService->getInfo($outputFile);
        self::assertEquals(2, $info->countStreams());
        $stream = $info->getVideoStreams()->getFirst();
        self::assertEquals('yuv420p', $stream->getPixFmt());
        self::assertEquals(StreamTypeInterface::VIDEO, $stream->getCodecType());
        self::assertEquals('vp9', $stream->getCodecName());
        // because it's vp9 with min/max bitrates ;)
        self::assertNull($stream->getBitRate());

        $audio = $info->getAudioStreams()->getFirst();
        self::assertEquals('opus', $audio->getCodecName());

        unlink($outputFile);
    }

    public function testConvertVP9MultiPass(): void
    {
        $outputFile = "{$this->outputDir}/testConvertVP9Multipass.webm";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $logFile = tempnam($this->outputDir, 'ffmpeg-passlog');
        if ($logFile === false) {
            self::markTestIncomplete('Cannot get a temp file');
        }

        $pass1Params = (new VideoConvertParams())
            ->withPassLogFile($logFile)
            ->withPass(1)
            ->withVideoCodec('libvpx-vp9')
            ->withVideoBitrate('200k') // target bitrate
            ->withVideoMaxBitrate('250k') // max bitrate
            ->withVideoMinBitrate('150k') // min bitrate
            ->withVideoFilter(new YadifVideoFilter())
            ->withThreads(0) // 0 means threads = number of cores
            ->withSpeed(4)
            ->withNoAudio()
            ->withKeyframeSpacing(240)
            ->withTileColumns(1)
            ->withFrameParallel(1)
            ->withPixFmt('yuv420p')
            // Will speed up - takes only 2 seconds
            ->withSeekStart(new SeekTime(1))
            ->withSeekEnd(new SeekTime(3))
            ->withOutputFormat('webm');

        $this->videoConvert->convert(
            $this->videoFile,
            new PlatformNullFile(),
            $pass1Params
        );

        self::assertFileExists($logFile);

        $pass2Params = $pass1Params
            ->withoutParam(VideoConvertParamsInterface::PARAM_NOAUDIO)
            ->withSpeed(1)
            ->withPass(2)
            ->withAutoAltRef(1)
            ->withLagInFrames(25)
            ->withAudioCodec('libopus')
            ->withAudioBitrate('96k');

        $this->videoConvert->convert(
            $this->videoFile,
            $outputFile,
            $pass2Params
        );

        unlink($logFile);
        self::assertFileExists($outputFile);

        $info   = $this->infoService->getInfo($outputFile);
        $stream = $info->getVideoStreams()->getFirst();
        self::assertEquals('yuv420p', $stream->getPixFmt());

        unlink($outputFile);
    }

    public function testConvertWithWrongPassWillError(): void
    {
        self::expectException(ProcessFailedException::class);
        self::expectExceptionMessageRegExp('/Error reading log file(.*)for pass-2 encoding/');

        $outputFile = "{$this->outputDir}/testConvertWithWrongPassWillError.webm";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $logFile = tempnam($this->outputDir, 'ffmpeg-passlog');
        if ($logFile === false) {
            self::markTestIncomplete('Cannot get a temp file');
        }

        $params = (new VideoConvertParams())
            ->withPassLogFile($logFile)
            ->withPass(2)
            ->withVideoCodec('libvpx-vp9')
            ->withOutputFormat('webm');

        $this->videoConvert->convert(
            $this->videoFile,
            new PlatformNullFile(),
            $params
        );
    }

    public function testConvertWithInvalidPassLogFileWillError(): void
    {
        $outputFile = "{$this->outputDir}/testConvertWithInvalidPassLogFileWillError.webm";

        self::expectException(ProcessFailedException::class);
        self::expectExceptionMessageRegExp('/Error reading log file(.*)for pass-2 encoding/');

        $logFile = $this->outputDir . '/testConvertWithInvalidLogFile-ffmpeg-passlog';
        if (file_exists($logFile)) {
            unlink($logFile);
        }

        $pass1Params = (new VideoConvertParams())
            ->withPassLogFile($logFile)
            ->withPass(1)
            ->withVideoCodec('libvpx-vp9')
            ->withVideoBitrate('200k') // target bitrate
            ->withVideoMaxBitrate('250k') // max bitrate
            ->withVideoMinBitrate('150k') // min bitrate
            ->withVideoFilter(new YadifVideoFilter())
            ->withThreads(0) // 0 means threads = number of cores
            ->withSpeed(4)
            ->withNoAudio()
            ->withKeyframeSpacing(240)
            ->withTileColumns(1)
            ->withFrameParallel(1)
            ->withPixFmt('yuv420p')
            // Will speed up - takes only 2 seconds
            ->withSeekStart(new SeekTime(1))
            ->withSeekEnd(new SeekTime(3))
            ->withOutputFormat('webm');

        $this->videoConvert->convert(
            $this->videoFile,
            new PlatformNullFile(),
            $pass1Params
        );

        $pass2Params = $pass1Params
            ->withoutParam(VideoConvertParamsInterface::PARAM_NOAUDIO)
            ->withPassLogFile($logFile . '.incorrect.log')
            ->withSpeed(1)
            ->withPass(2)
            ->withAutoAltRef(1)
            ->withLagInFrames(25)
            ->withAudioCodec('libopus')
            ->withAudioBitrate('96k');

        $this->videoConvert->convert(
            $this->videoFile,
            $outputFile,
            $pass2Params
        );
    }

    public function testConvertMustThrowFileNotFoundException(): void
    {
        self::expectException(MissingInputFileException::class);
        $this->videoConvert->convert('/no_exists/test.mov', '/tmp/test.mp4', new VideoConvertParams());
    }

    public function testConvertInvalidCodecMustThrowProcessException(): void
    {
        self::expectException(ProcessExceptionInterface::class);

        $outputFile = "{$this->outputDir}/testBasicUsageThrowsProcessConversionException.tmp.mp4";

        $params = (new VideoConvertParams())->withVideoCodec('NOVALIDCODEC');

        $this->videoConvert->convert($this->videoFile, $outputFile, $params);
    }

    public function testConvertInvalidFileMustThrowProcessException(): void
    {
        self::expectException(ProcessExceptionInterface::class);

        $outputFile = "{$this->outputDir}/testBasicUsageThrowsProcessConversionException.tmp.mp4";

        $params = (new VideoConvertParams())->withVideoCodec('NOVALIDCODEC');

        $this->videoConvert->convert("{$this->baseDir}/data/not_a_video_file.mov", $outputFile, $params);
    }

    public function testConvertMustThrowExceptionOnDefaultTimeout(): void
    {
        self::expectException(ProcessTimedOutException::class);

        $outputFile = "{$this->outputDir}/throwExceptionOnGlobalTimeout.tmp.mp4";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
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

        $convertParams = (new VideoConvertParams())
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

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('h264');

        $processParams = new ProcessParams(null, 0.1, []);

        $this->videoConvert->convert($this->videoFile, $outputFile, $convertParams, null, $processParams);
    }
}
