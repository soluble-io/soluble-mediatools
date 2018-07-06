<?php

declare(strict_types=1);

namespace MediaToolsTest\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Converter\ParamsInterface;
use Soluble\MediaTools\Video\ConverterParams;
use Soluble\MediaTools\Video\Filter\VideoFilterInterface;

class ConverterParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustBeImmutable(): void
    {
        $params = new ConverterParams();
        self::assertCount(0, $params->getOptions());
        $newParams = $params->withThreads(1);
        self::assertCount(0, $params->getOptions());
        self::assertCount(1, $newParams->getOptions());
    }

    public function testHasOption(): void
    {
        $params = (new ConverterParams())
                  ->withTileColumns(10);
        self::assertTrue($params->hasOption(ParamsInterface::PARAM_TILE_COLUMNS));
        self::assertFalse($params->hasOption(ParamsInterface::PARAM_FRAME_PARALLEL));
    }

    public function testWithParamsMustBeIdenticalToConstrutorInject(): void
    {
        $injectedParams = new ConverterParams([
            ParamsInterface::PARAM_TUNE => 'grain',
        ]);

        $withParams = (new ConverterParams())->withTune('grain');

        self::assertSame($injectedParams->getOptions(), $withParams->getOptions());
    }

    public function testGetOptionsMustEqualsParams(): void
    {
        $params = (new ConverterParams())
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
            ParamsInterface::PARAM_TILE_COLUMNS      => 10,
            ParamsInterface::PARAM_THREADS           => 8,
            ParamsInterface::PARAM_SPEED             => 2,
            ParamsInterface::PARAM_KEYFRAME_SPACING  => 240,
            ParamsInterface::PARAM_CRF               => 32,
            ParamsInterface::PARAM_OUTPUT_FORMAT     => 'mp4',
            ParamsInterface::PARAM_VIDEO_MIN_BITRATE => '750k',
            ParamsInterface::PARAM_VIDEO_BITRATE     => '1M',
            ParamsInterface::PARAM_QUALITY           => 'good',
            ParamsInterface::PARAM_STREAMABLE        => true,
            ParamsInterface::PARAM_PIX_FMT           => 'yuv420p',
            ParamsInterface::PARAM_PRESET            => 'fast',
            ParamsInterface::PARAM_AUDIO_BITRATE     => '128k',
            ParamsInterface::PARAM_AUDIO_CODEC       => 'aac',
            ParamsInterface::PARAM_VIDEO_CODEC       => 'h264',
            ParamsInterface::PARAM_VIDEO_MAX_BITRATE => '2000000',
            ParamsInterface::PARAM_FRAME_PARALLEL    => 2,
            ParamsInterface::PARAM_TUNE              => 'film',
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
        $params = (new ConverterParams())
            ->withTileColumns(10)
            ->withTileColumns(12);

        self::assertEquals([
            ParamsInterface::PARAM_TILE_COLUMNS      => 12,
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

        $params = (new ConverterParams())
            ->withVideoFilter($filter1);

        self::assertSame($filter1, $params->getOption(ParamsInterface::PARAM_VIDEO_FILTER));
        self::assertEquals('-vf filter_1', $params->getFFMpegArguments()[ParamsInterface::PARAM_VIDEO_FILTER]);
    }

    public function testUnsupportedParamThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);
        new ConverterParams(['UnsupportedOption' => 'cool']);
    }

    public function testInvalidBitRateThrowsInvalidArgumentException(): void
    {
        $params = new ConverterParams();

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
