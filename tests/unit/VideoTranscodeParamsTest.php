<?php

declare(strict_types=1);

namespace MediaToolsTest;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\VideoTranscodeParams;

class VideoTranscodeParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustBeImmutable(): void
    {
        $params = new VideoTranscodeParams();
        self::assertCount(0, $params->getOptions());
        $newParams = $params->withThreads(1);
        self::assertCount(0, $params->getOptions());
        self::assertCount(1, $newParams->getOptions());
    }

    public function testParams(): void
    {
        $params = (new VideoTranscodeParams())
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
            ->withPreset('best')
            ->withAudioBitrate('128k')
            ->withAudioCodec('aac')
            ->withVideoCodec('h264')
            ->withVideoMaxBitrate('2M')
            ->withFrameParallel(2);

        self::assertEquals([
            VideoTranscodeParams::OPTION_TILE_COLUMNS      => 10,
            VideoTranscodeParams::OPTION_THREADS           => 8,
            VideoTranscodeParams::OPTION_SPEED             => 2,
            VideoTranscodeParams::OPTION_KEYFRAME_SPACING  => 240,
            VideoTranscodeParams::OPTION_CRF               => 32,
            VideoTranscodeParams::OPTION_OUTPUT_FORMAT     => 'mp4',
            VideoTranscodeParams::OPTION_VIDEO_MIN_BITRATE => '750k',
            VideoTranscodeParams::OPTION_VIDEO_BITRATE     => '1M',
            VideoTranscodeParams::OPTION_QUALITY           => 'good',
            VideoTranscodeParams::OPTION_STREAMABLE        => true,
            VideoTranscodeParams::OPTION_PIX_FMT           => 'yuv420p',
            VideoTranscodeParams::OPTION_PRESET            => 'best',
            VideoTranscodeParams::OPTION_AUDIO_BITRATE     => '128k',
            VideoTranscodeParams::OPTION_AUDIO_CODEC       => 'aac',
            VideoTranscodeParams::OPTION_VIDEO_CODEC       => 'h264',
            VideoTranscodeParams::OPTION_VIDEO_MAX_BITRATE => '2M',
            VideoTranscodeParams::OPTION_FRAME_PARALLEL    => 2,
        ], $params->getOptions());
    }
}
