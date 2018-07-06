<?php

declare(strict_types=1);

namespace MediaToolsTest;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Filter\VideoFilterInterface;
use Soluble\MediaTools\VideoConvertParams;

class VideoConvertParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustBeImmutable(): void
    {
        $params = new VideoConvertParams();
        self::assertCount(0, $params->getOptions());
        $newParams = $params->withThreads(1);
        self::assertCount(0, $params->getOptions());
        self::assertCount(1, $newParams->getOptions());
    }

    public function testHasOption(): void
    {
        $params = (new VideoConvertParams())
                  ->withTileColumns(10);
        self::assertTrue($params->hasOption(VideoConvertParams::OPTION_TILE_COLUMNS));
        self::assertFalse($params->hasOption(VideoConvertParams::OPTION_FRAME_PARALLEL));
    }

    public function testWithParamsMustBeIdenticalToConstrutorInject(): void
    {
        $injectedParams = new VideoConvertParams([
            VideoConvertParams::OPTION_TUNE => 'grain',
        ]);

        $withParams = (new VideoConvertParams())->withTune('grain');

        self::assertSame($injectedParams->getOptions(), $withParams->getOptions());
    }

    public function testGetOptionsMustEqualsParams(): void
    {
        $params = (new VideoConvertParams())
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
            ->withTune('film');

        $expectedOptions = [
            VideoConvertParams::OPTION_TILE_COLUMNS      => 10,
            VideoConvertParams::OPTION_THREADS           => 8,
            VideoConvertParams::OPTION_SPEED             => 2,
            VideoConvertParams::OPTION_KEYFRAME_SPACING  => 240,
            VideoConvertParams::OPTION_CRF               => 32,
            VideoConvertParams::OPTION_OUTPUT_FORMAT     => 'mp4',
            VideoConvertParams::OPTION_VIDEO_MIN_BITRATE => '750k',
            VideoConvertParams::OPTION_VIDEO_BITRATE     => '1M',
            VideoConvertParams::OPTION_QUALITY           => 'good',
            VideoConvertParams::OPTION_STREAMABLE        => true,
            VideoConvertParams::OPTION_PIX_FMT           => 'yuv420p',
            VideoConvertParams::OPTION_PRESET            => 'fast',
            VideoConvertParams::OPTION_AUDIO_BITRATE     => '128k',
            VideoConvertParams::OPTION_AUDIO_CODEC       => 'aac',
            VideoConvertParams::OPTION_VIDEO_CODEC       => 'h264',
            VideoConvertParams::OPTION_VIDEO_MAX_BITRATE => '2000000',
            VideoConvertParams::OPTION_FRAME_PARALLEL    => 2,
            VideoConvertParams::OPTION_TUNE              => 'film',
        ];

        self::assertEquals($expectedOptions, $params->getOptions());

        foreach ($expectedOptions as $key => $value) {
            self::assertEquals($value, $params->getOption($key));
        }

        $cli = '-tile-columns 10 -threads 8 -speed 2 -g 240 -crf 32 -f mp4 ' .
               '-minrate 750k -b:v 1M -quality good -movflags +faststart -pix_fmt yuv420p ' .
               '-preset fast -b:a 128k -acodec aac -vcodec h264 -maxrate 2000000 ' .
                '-frame-parallel 2 -tune film';
        self::assertEquals($cli, implode(' ', $params->getFFMpegArguments()));
    }

    public function testNewParamMustOverwritePreviousParam(): void
    {
        $params = (new VideoConvertParams())
            ->withTileColumns(10)
            ->withTileColumns(12);

        self::assertEquals([
            VideoConvertParams::OPTION_TILE_COLUMNS      => 12,
        ], $params->getOptions());
    }

    public function testWithVideoFilter(): void
    {
        $filter1 = new class() implements VideoFilterInterface {
            public function getFFMpegCLIArgument(): string
            {
                return '-vf';
            }

            public function getFFmpegCLIValue(): string
            {
                return 'filter_1';
            }
        };

        $params = (new VideoConvertParams())
            ->withVideoFilter($filter1);

        self::assertSame($filter1, $params->getOption(VideoConvertParams::OPTION_VIDEO_FILTER));
        self::assertEquals('-vf filter_1', $params->getFFMpegArguments()[VideoConvertParams::OPTION_VIDEO_FILTER]);
    }

    public function testUnsupportedParamThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);
        new VideoConvertParams(['UnsupportedOption' => 'cool']);
    }

    public function testInvalidBitRateThrowsInvalidArgumentException(): void
    {
        $params = new VideoConvertParams();

        try {
            $params->withVideoBitrate('901w');
            self::fail('Invalid bitrate must throw an exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidArgumentException::class, $e);
        }

        try {
            $params->withVideoBitrate('');
            self::fail('Invalid bitrate must throw an exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidArgumentException::class, $e);
        }

        try {
            $params->withAudioBitrate('12M1');
            self::fail('Invalid bitrate must throw an exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidArgumentException::class, $e);
        }

        try {
            $params->withVideoMaxBitrate('12MM');
            self::fail('Invalid bitrate must throw an exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidArgumentException::class, $e);
        }

        try {
            $params->withVideoMinBitrate('12MK');
            self::fail('Invalid bitrate must throw an exception');
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidArgumentException::class, $e);
        }
    }
}
