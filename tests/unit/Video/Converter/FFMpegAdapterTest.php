<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Converter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\Converter\FFMpegAdapter;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\VideoConversionParams;

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

        $conversionParams = (new VideoConversionParams())
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
            ->withSeekEnd($seekTimeEnd);

        $expectedCli = '-tile-columns 10 -threads 8 -speed 2 -g 240 -crf 32 -f mp4 ' .
               '-minrate 750k -b:v 1M -quality good -movflags +faststart -pix_fmt yuv420p ' .
               '-preset fast -b:a 128k -c:a aac -c:v h264 -maxrate 2000000 ' .
               '-frame-parallel 2 -tune film -y -an -frames:v 1500 ' .
               '-ss 0:00:00.1 -to 0:00:00.6';

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);

        self::assertEquals($expectedCli, implode(' ', $args));
    }

    public function testOverwiteSupport(): void
    {
        $conversionParams = (new VideoConversionParams());

        self::assertEquals('-y', implode(
            ' ',
            $this->ffmpegAdapter->getMappedConversionParams($conversionParams)
        ));

        $conversionParams = (new VideoConversionParams())->withOverwrite();

        self::assertEquals('-y', implode(
            ' ',
            $this->ffmpegAdapter->getMappedConversionParams($conversionParams)
        ));

        $conversionParams = (new VideoConversionParams())->withNoOverwrite();
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

        $conversionParams = (new VideoConversionParams())
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

        $conversionParams = (new VideoConversionParams())
            ->withVideoFilter($filterChain);

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);
        self::assertEquals('-vf filter_1,filter_2', $args[ConversionParamsInterface::PARAM_VIDEO_FILTER]);
    }

    public function testWithNonFFMpegVideoFilterMustThrowException(): void
    {
        self::expectException(UnsupportedParamValueException::class);
        $filter1 = new class() implements VideoFilterInterface {
        };

        $conversionParams = (new VideoConversionParams())
            ->withVideoFilter($filter1);

        $args = $this->ffmpegAdapter->getMappedConversionParams($conversionParams);
        self::assertEquals('-vf filter_1', $args[ConversionParamsInterface::PARAM_VIDEO_FILTER]);
    }
}
