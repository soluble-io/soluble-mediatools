<?php

declare(strict_types=1);

namespace MediaToolsTest\Video;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\ConversionParams;
use Soluble\MediaTools\Video\ConversionService;
use Soluble\MediaTools\Video\ConversionServiceInterface;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\Hqdn3DVideoFilter;
use Soluble\MediaTools\Video\Filter\NlmeansVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;
use Soluble\MediaTools\Video\SeekTime;

class ConversionServiceTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var ConversionServiceInterface */
    protected $converter;

    public function setUp(): void
    {
        $this->converter = $this->getVideoConvertService();
    }

    public function testGetSymfonyProcessMustReturnCorrectParams(): void
    {
        $videoFilterChain = new VideoFilterChain();
        $videoFilterChain->addFilter(new EmptyVideoFilter());
        $videoFilterChain->addFilter(new YadifVideoFilter());
        $videoFilterChain->addFilter(new Hqdn3DVideoFilter());
        $videoFilterChain->addFilter(new NlmeansVideoFilter());

        $convertParams = (new ConversionParams())
            ->withVideoCodec('libvpx-vp9')
            ->withCrf(32)
            ->withVideoBitrate('200k')
            ->withVideoMaxBitrate('250000')
            ->withVideoMinBitrate('150k')
            ->withAudioCodec('libopus')
            ->withAudioBitrate('96k')
            ->withVideoFilter($videoFilterChain)
            ->withThreads(12)
            ->withSpeed(8)
            ->withKeyframeSpacing(240)
            ->withTileColumns(1)
            ->withFrameParallel(1)
            ->withPixFmt('yuv420p')
            ->withSeekStart(new SeekTime(1))
            ->withVideoFrames(200)
            ->withOutputFormat('webm');

        $process = $this->converter->getSymfonyProcess(
            __FILE__,
            '/path/output',
            $convertParams
        );

        $cmdLine = $process->getCommandLine();

        self::assertContains(' -c:v libvpx-vp9 ', $cmdLine);
        self::assertContains(' -b:v 200k ', $cmdLine);
        self::assertContains(' -maxrate 250000', $cmdLine);
        self::assertContains(' -minrate 150k ', $cmdLine);
        self::assertContains(' -c:a libopus ', $cmdLine);
        self::assertContains(' -b:a 96k ', $cmdLine);
        self::assertContains(' -vf yadif=mode=0:parity=-1:deint=0,hqdn3d,nlmeans ', $cmdLine);
        self::assertContains(' -threads 12 ', $cmdLine);
        self::assertContains(' -speed 8 ', $cmdLine);
        self::assertContains(' -g 240 ', $cmdLine);
        self::assertContains(' -tile-columns 1 ', $cmdLine);
        self::assertContains(' -frame-parallel 1', $cmdLine);
        self::assertContains(' -pix_fmt yuv420p ', $cmdLine);
        self::assertContains(' -f webm ', $cmdLine);
        self::assertContains(' -ss 0:00:01.0 ', $cmdLine);
        self::assertContains(' -frames:v 200 ', $cmdLine);
    }

    public function testGetSymfonyProcessMustDefaultToConfigThreads(): void
    {
        $convertParams = (new ConversionParams());

        $process = (new ConversionService(
            new FFMpegConfig('ffmpeg', 3)
        ))->getSymfonyProcess(
            __FILE__,
            '/path/output',
            $convertParams
        );

        $cmdLine = $process->getCommandLine();

        self::assertContains(' -threads 3 ', $cmdLine);

        // If null threads nothing must be set in cli

        $process = (new ConversionService(
            new FFMpegConfig('ffmpeg', null)
        ))->getSymfonyProcess(
            __FILE__,
            '/path/output',
            $convertParams
        );

        $cmdLine = $process->getCommandLine();

        self::assertNotContains(' -threads ', $cmdLine);
    }
}
