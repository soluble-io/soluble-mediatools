<?php

declare(strict_types=1);

namespace MediaToolsTest;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\VideoConversionParams;

class VideoConversionParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustBeImmutable(): void
    {
        $params = new VideoConversionParams();
        self::assertCount(0, $params->toArray());
        $newParams = $params->withThreads(1);
        self::assertCount(0, $params->toArray());
        self::assertCount(1, $newParams->toArray());
    }

    public function testHasParam(): void
    {
        $params = (new VideoConversionParams())
                  ->withTileColumns(10);
        self::assertTrue($params->hasParam(ConversionParamsInterface::PARAM_TILE_COLUMNS));
        self::assertFalse($params->hasParam(ConversionParamsInterface::PARAM_FRAME_PARALLEL));
    }

    public function testWithParamsMustBeIdenticalToConstrutorInject(): void
    {
        $injectedParams = new VideoConversionParams([
            ConversionParamsInterface::PARAM_TUNE => 'grain',
        ]);

        $withParams = (new VideoConversionParams())->withTune('grain');

        self::assertSame($injectedParams->toArray(), $withParams->toArray());
    }

    public function testGetOptionsMustEqualsParams(): void
    {
        $seekTimeStart = new SeekTime(0.1);
        $seekTimeEnd   = new SeekTime(0.6);

        $params = (new VideoConversionParams())
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
            ->withOverwriteFile()
            ->withNoAudio()
            ->withVideoFrames(1500)
            ->withFilter('idet')
            ->withSeekStart($seekTimeStart)
            ->withSeekEnd($seekTimeEnd);

        $expectedParams = [
            ConversionParamsInterface::PARAM_TILE_COLUMNS      => 10,
            ConversionParamsInterface::PARAM_THREADS           => 8,
            ConversionParamsInterface::PARAM_SPEED             => 2,
            ConversionParamsInterface::PARAM_KEYFRAME_SPACING  => 240,
            ConversionParamsInterface::PARAM_CRF               => 32,
            ConversionParamsInterface::PARAM_OUTPUT_FORMAT     => 'mp4',
            ConversionParamsInterface::PARAM_VIDEO_MIN_BITRATE => '750k',
            ConversionParamsInterface::PARAM_VIDEO_BITRATE     => '1M',
            ConversionParamsInterface::PARAM_QUALITY           => 'good',
            ConversionParamsInterface::PARAM_STREAMABLE        => true,
            ConversionParamsInterface::PARAM_PIX_FMT           => 'yuv420p',
            ConversionParamsInterface::PARAM_PRESET            => 'fast',
            ConversionParamsInterface::PARAM_AUDIO_BITRATE     => '128k',
            ConversionParamsInterface::PARAM_AUDIO_CODEC       => 'aac',
            ConversionParamsInterface::PARAM_VIDEO_CODEC       => 'h264',
            ConversionParamsInterface::PARAM_VIDEO_MAX_BITRATE => '2000000',
            ConversionParamsInterface::PARAM_FRAME_PARALLEL    => 2,
            ConversionParamsInterface::PARAM_TUNE              => 'film',
            ConversionParamsInterface::PARAM_OVERWRITE_FILE    => true,
            ConversionParamsInterface::PARAM_NOAUDIO           => true,
            ConversionParamsInterface::PARAM_VIDEO_FRAMES      => 1500,
            ConversionParamsInterface::PARAM_FILTER            => 'idet',

            ConversionParamsInterface::PARAM_SEEK_START        => $seekTimeStart,
            ConversionParamsInterface::PARAM_SEEK_END          => $seekTimeEnd,
        ];

        self::assertEquals($expectedParams, $params->toArray());

        foreach ($expectedParams as $key => $value) {
            self::assertEquals($value, $params->getParam($key));
        }
    }

    public function testNewParamMustOverwritePreviousParam(): void
    {
        $params = (new VideoConversionParams())
            ->withTileColumns(10)
            ->withTileColumns(12);

        self::assertEquals([
            ConversionParamsInterface::PARAM_TILE_COLUMNS      => 12,
        ], $params->toArray());
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

        $params = (new VideoConversionParams())
            ->withVideoFilter($filter1);

        self::assertSame($filter1, $params->getParam(ConversionParamsInterface::PARAM_VIDEO_FILTER));
    }

    public function testUnsupportedParamThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);
        new VideoConversionParams(['UnsupportedOption' => 'cool']);
    }

    public function testInvalidBitRateThrowsInvalidArgumentException(): void
    {
        $params = new VideoConversionParams();

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
