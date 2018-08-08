<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Adapter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\InvalidArgumentException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Video\Adapter\FFMpegAdapter;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\ConversionParams;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\SeekTime;

class FFMpegAdapterTest extends TestCase
{
    /** @var FFMpegAdapter */
    protected $ffmpegAdapter;

    public function setUp(): void
    {
        $this->ffmpegAdapter = new FFMpegAdapter(
            new FFMpegConfig('ffmpeg', 0, 10, 5, [])
        );
    }

    public function testConversionOptionsMustGiveCorretCliArguments(): void
    {
        $seekTimeStart = new SeekTime(0.1);
        $seekTimeEnd   = new SeekTime(0.6);

        $conversionParams = (new ConversionParams())
            ->withTileColumns(10)
            ->withThreads(8)
            ->withSpeed(2)
            ->withKeyframeSpacing(240)
            ->withCrf(32)
            ->withOutputFormat('mp4')
            ->withVideoMinBitrate('750k')
            ->withVideoBitrate('1M')
            ->withQuality('good')
            ->withStreamable(true)
            ->withPixFmt('yuv420p')
            ->withPreset('fast')
            ->withAudioBitrate('128k')
            ->withAudioCodec('aac')
            ->withVideoCodec('h264')
            ->withVideoMaxBitrate('2000000')
            ->withFrameParallel(2)
            ->withTune('film')
            ->withOverwrite()
            ->withNoAudio()
            ->withVideoFrames(1500)
            ->withSeekStart($seekTimeStart)
            ->withSeekEnd($seekTimeEnd)
            ->withVideoQualityScale(1);

        $expectedCli = '-tile-columns 10 -threads 8 -speed 2 -g 240 -crf 32 -f mp4 ' .
               '-minrate 750k -b:v 1M -quality good -movflags +faststart -pix_fmt yuv420p ' .
               '-preset fast -b:a 128k -c:a aac -c:v h264 -maxrate 2000000 ' .
               '-frame-parallel 2 -tune film -y -an -frames:v 1500 ' .
               '-ss 0:00:00.1 -to 0:00:00.6 -qscale:v 1';

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);

        self::assertEquals($expectedCli, implode(' ', $args));
    }

    public function testOverwiteSupport(): void
    {
        $conversionParams = (new ConversionParams());

        self::assertEquals('-y', implode(
            ' ',
            $this->ffmpegAdapter->getMappedConversionParams($conversionParams)
        ));

        $conversionParams = (new ConversionParams())->withOverwrite();

        self::assertEquals('-y', implode(
            ' ',
            $this->ffmpegAdapter->getMappedConversionParams($conversionParams)
        ));

        $conversionParams = (new ConversionParams())->withNoOverwrite();
        $args             = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);
        self::assertEquals('', implode(' ', $args));
    }

    public function testWithFFMpegVideoFilter(): void
    {
        $filter1 = new class() implements FFMpegVideoFilterInterface {
            public function getFFmpegCLIValue(): string
            {
                return 'filter_1';
            }
        };

        $conversionParams = (new ConversionParams())
            ->withVideoFilter($filter1);

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);
        self::assertEquals('-vf filter_1', $args[ConversionParamsInterface::PARAM_VIDEO_FILTER]);
    }

    public function testWithFFMpegVideoFilterChain(): void
    {
        $filter1 = new class() implements FFMpegVideoFilterInterface {
            public function getFFmpegCLIValue(): string
            {
                return 'filter_1';
            }
        };

        $filter2 = new class() implements FFMpegVideoFilterInterface {
            public function getFFmpegCLIValue(): string
            {
                return 'filter_2';
            }
        };

        $filterChain = new VideoFilterChain();
        $filterChain->addFilter($filter1);
        $filterChain->addFilter($filter2);

        $conversionParams = (new ConversionParams())
            ->withVideoFilter($filterChain);

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);
        self::assertEquals('-vf filter_1,filter_2', $args[ConversionParamsInterface::PARAM_VIDEO_FILTER]);
    }

    public function testWithNonFFMpegVideoFilterMustThrowException(): void
    {
        self::expectException(UnsupportedParamValueException::class);
        $filter1 = new class() implements VideoFilterInterface {
        };

        $conversionParams = (new ConversionParams())
            ->withVideoFilter($filter1);

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);
        self::assertEquals('-vf filter_1', $args[ConversionParamsInterface::PARAM_VIDEO_FILTER]);
    }

    public function testGetCliCommand(): void
    {
        $params = (new ConversionParams())
            ->withCrf(32)
            ->withVideoCodec('h264');

        $cmd = $this->ffmpegAdapter->getCliCommand(
            $this->ffmpegAdapter->getMappedConversionParams($params),
            '/test/video.mp4',
            '/test/output.mp4'
        );

        self::assertContains('ffmpeg -i \'/test/video.mp4\' -crf 32 -c:v h264 -y \'/test/output.mp4\'', $cmd);
    }

    public function testGetCliCommandWrongOutputFileThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);

        $params = (new ConversionParams())
            ->withCrf(32)
            ->withVideoCodec('h264');

        $this->ffmpegAdapter->getCliCommand(
                    $this->ffmpegAdapter->getMappedConversionParams($params),
                    '/test/video.mp4',
                    ['invalid_output_file']
            );
    }
}
