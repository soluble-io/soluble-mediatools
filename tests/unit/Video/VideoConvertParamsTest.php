<?php

declare(strict_types=1);

namespace MediaToolsTest\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\UnsetParamReaderException;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoConvertParams;
use Soluble\MediaTools\Video\VideoConvertParamsInterface;

class VideoConvertParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustBeImmutable(): void
    {
        $params = new VideoConvertParams();
        self::assertCount(0, $params->toArray());
        $newParams = $params->withThreads(1);
        self::assertCount(0, $params->toArray());
        self::assertCount(1, $newParams->toArray());
    }

    public function testWithoutParam(): void
    {
        $params = (new VideoConvertParams())
            ->withTileColumns(10)
            ->withThreads(10);

        $newParams = $params->withoutParam(VideoConvertParams::PARAM_THREADS);
        self::assertTrue($newParams->hasParam(VideoConvertParamsInterface::PARAM_TILE_COLUMNS));
        self::assertFalse($newParams->hasParam(VideoConvertParamsInterface::PARAM_THREADS));
        self::assertTrue($params->hasParam(VideoConvertParamsInterface::PARAM_THREADS));
    }

    public function testHasParam(): void
    {
        $params = (new VideoConvertParams())
                  ->withTileColumns(10);
        self::assertTrue($params->hasParam(VideoConvertParamsInterface::PARAM_TILE_COLUMNS));
        self::assertFalse($params->hasParam(VideoConvertParamsInterface::PARAM_FRAME_PARALLEL));
    }

    public function testWithConvertParams(): void
    {
        $params1 = (new VideoConvertParams())
            ->withTileColumns(10);

        self::assertEquals(
            10,
            (new VideoConvertParams())
                ->withConvertParams($params1)
                ->getParam(VideoConvertParamsInterface::PARAM_TILE_COLUMNS)
        );

        $params2 = (new VideoConvertParams())
            ->withTileColumns(30);

        self::assertEquals(
            30,
                $params1->withConvertParams($params2)
                        ->getParam(VideoConvertParamsInterface::PARAM_TILE_COLUMNS)
        );
    }

    public function testWithParamsMustBeIdenticalToConstrutorInject(): void
    {
        $injectedParams = new VideoConvertParams([
            VideoConvertParamsInterface::PARAM_TUNE => 'grain',
        ]);

        $withParams = (new VideoConvertParams())->withTune('grain');

        self::assertSame($injectedParams->toArray(), $withParams->toArray());
    }

    public function testGetOptionsMustEqualsParams(): void
    {
        $seekTimeStart = new SeekTime(0.1);
        $seekTimeEnd   = new SeekTime(0.6);

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
            ->withTune('film')
            ->withOverwrite()
            ->withNoAudio()
            ->withVideoFrames(1500)
            ->withSeekStart($seekTimeStart)
            ->withSeekEnd($seekTimeEnd);

        $expectedParams = [
            VideoConvertParamsInterface::PARAM_TILE_COLUMNS      => 10,
            VideoConvertParamsInterface::PARAM_THREADS           => 8,
            VideoConvertParamsInterface::PARAM_SPEED             => 2,
            VideoConvertParamsInterface::PARAM_KEYFRAME_SPACING  => 240,
            VideoConvertParamsInterface::PARAM_CRF               => 32,
            VideoConvertParamsInterface::PARAM_OUTPUT_FORMAT     => 'mp4',
            VideoConvertParamsInterface::PARAM_VIDEO_MIN_BITRATE => '750k',
            VideoConvertParamsInterface::PARAM_VIDEO_BITRATE     => '1M',
            VideoConvertParamsInterface::PARAM_QUALITY           => 'good',
            VideoConvertParamsInterface::PARAM_STREAMABLE        => true,
            VideoConvertParamsInterface::PARAM_PIX_FMT           => 'yuv420p',
            VideoConvertParamsInterface::PARAM_PRESET            => 'fast',
            VideoConvertParamsInterface::PARAM_AUDIO_BITRATE     => '128k',
            VideoConvertParamsInterface::PARAM_AUDIO_CODEC       => 'aac',
            VideoConvertParamsInterface::PARAM_VIDEO_CODEC       => 'h264',
            VideoConvertParamsInterface::PARAM_VIDEO_MAX_BITRATE => '2000000',
            VideoConvertParamsInterface::PARAM_FRAME_PARALLEL    => 2,
            VideoConvertParamsInterface::PARAM_TUNE              => 'film',
            VideoConvertParamsInterface::PARAM_OVERWRITE         => true,
            VideoConvertParamsInterface::PARAM_NOAUDIO           => true,
            VideoConvertParamsInterface::PARAM_VIDEO_FRAMES      => 1500,

            VideoConvertParamsInterface::PARAM_SEEK_START        => $seekTimeStart,
            VideoConvertParamsInterface::PARAM_SEEK_END          => $seekTimeEnd,
        ];

        self::assertEquals($expectedParams, $params->toArray());

        foreach ($expectedParams as $key => $value) {
            self::assertEquals($value, $params->getParam($key));
        }
    }

    public function testNewParamMustOverwritePreviousParam(): void
    {
        $params = (new VideoConvertParams())
            ->withTileColumns(10)
            ->withTileColumns(12);

        self::assertEquals([
            VideoConvertParamsInterface::PARAM_TILE_COLUMNS      => 12,
        ], $params->toArray());
    }

    public function testWithBuiltInParam(): void
    {
        $params = (new VideoConvertParams())
                    ->withBuiltInParam(VideoConvertParams::PARAM_TILE_COLUMNS, 12);

        self::assertEquals([
            VideoConvertParamsInterface::PARAM_TILE_COLUMNS      => 12,
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

        $params = (new VideoConvertParams())
            ->withVideoFilter($filter1);

        self::assertSame($filter1, $params->getParam(VideoConvertParamsInterface::PARAM_VIDEO_FILTER));
    }

    public function testGetParamThrowsUnsetParamException(): void
    {
        self::expectException(UnsetParamReaderException::class);

        $params = (new VideoConvertParams())->withTileColumns(10);

        $params->getParam(VideoConvertParams::PARAM_AUDIO_BITRATE);
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
