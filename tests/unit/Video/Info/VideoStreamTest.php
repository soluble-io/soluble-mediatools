<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Info;

use MediaToolsTest\Util\FFProbeMetadataProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Info\VideoStream;

class VideoStreamTest extends TestCase
{
    use FFProbeMetadataProviderTrait;

    public function setUp(): void
    {
    }

    public function testGetVideoStreams(): void
    {
        $videoStreamIdxs = [0, 1];

        foreach ($videoStreamIdxs as $idx) {
            $d      = $this->getExampleFFProbeData()['streams'][$idx];
            $stream = new VideoStream($d);

            self::assertEquals($d['index'], $stream->getIndex());

            self::assertEquals($d['nb_frames'] ?? null, $stream->getNbFrames());
            self::assertEquals($d['width'], $stream->getWidth());
            self::assertEquals($d['avg_frame_rate'] ?? null, $stream->getAvgFrameRate());
            self::assertEquals($d['bit_rate'] ?? null, $stream->getBitRate());
            self::assertEquals($d['codec_long_name'] ?? null, $stream->getCodecLongName());
            self::assertEquals($d['codec_name'], $stream->getCodecName());
            self::assertEquals($d['codec_tag_string'] ?? null, $stream->getCodecTagString());
            self::assertEquals($d['codec_time_base'] ?? null, $stream->getCodecTimeBase());

            self::assertEquals($d['coded_height'] ?? null, $stream->getCodedHeight());
            self::assertEquals($d['coded_width'] ?? null, $stream->getCodedWidth());
            self::assertEquals($d['color_range'] ?? null, $stream->getColorRange());
            self::assertEquals($d['color_space'] ?? null, $stream->getColorSpace());
            self::assertEquals($d['color_transfer'] ?? null, $stream->getColorTransfer());
            self::assertEquals($d['profile'] ?? null, $stream->getProfile());

            self::assertEquals($d['start_time'], $stream->getStartTime());
            self::assertEquals($d['display_aspect_ratio'], $stream->getDisplayAspectRatio());

            self::assertEquals($d['duration'], $stream->getDuration());
            self::assertEquals($d['duration_ts'] ?? null, $stream->getDurationTs());
            self::assertEquals($d['height'], $stream->getHeight());
            self::assertEquals($d['width'], $stream->getWidth());

            self::assertEquals($d['level'], $stream->getLevel());
            self::assertEquals($d['pix_fmt'] ?? null, $stream->getPixFmt());
            self::assertEquals($d['r_frame_rate'], $stream->getRFrameRate());

            self::assertEquals($d['sample_aspect_ratio'] ?? null, $stream->getSampleAspectRatio());
            self::assertEquals($d['tags'] ?? [], $stream->getTags());
            self::assertEquals($d['time_base'] ?? null, $stream->getTimeBase());
            self::assertEquals(($d['is_avc'] ?? null) === 'true', $stream->isAvc());

            self::assertEquals($d, $stream->getStreamMetadata());

            // Check aspect ratio
            $ar = $stream->getAspectRatio();
            self::assertNotNull($ar);
            self::assertEquals(16, $ar->getX());
            self::assertEquals(9, $ar->getY());
            self::assertEquals('16:9', $ar->getString(':'));
        }
    }

    public function testNullIsAvc(): void
    {
        $data = $this->getExampleFFProbeData()['streams'][0];
        unset($data['is_avc']);
        $stream = new VideoStream($data);
        self::assertNull($stream->isAvc());
    }

    public function testGetFps(): void
    {
        $data = $this->getExampleFFProbeData()['streams'][0];
        $d    = array_merge($data, [
            'r_frame_rate' => '25/1',
        ]);
        self::assertEquals(25, (new VideoStream($d))->getFps());

        $d = array_merge($data, [
            'r_frame_rate' => '24000/1001',
        ]);
        self::assertEquals(23.98, (new VideoStream($d))->getFps(2));

        $d = array_merge($data, [
            'r_frame_rate' => '24000/1001',
        ]);
        self::assertEquals(24, (new VideoStream($d))->getFps(0));

        $d = array_merge($data, [
            'r_frame_rate' => null,
            'duration'     => 1,
            'nb_frames'    => 30,
        ]);
        self::assertEquals(30, (new VideoStream($d))->getFps(0));

        $d = array_merge($data, [
            'r_frame_rate' => null,
            'duration'     => 1000,
            'nb_frames'    => 60002,
        ]);
        self::assertEquals(60.002, (new VideoStream($d))->getFps(3));

        $d = array_merge($data, [
            'r_frame_rate' => 'A/a',
            'duration'     => 1000,
            'nb_frames'    => 60002,
        ]);
        self::assertEquals(60.002, (new VideoStream($d))->getFps(3));

        $d = array_merge($data, [
            'r_frame_rate' => null,
            'duration'     => null,
            'nb_frames'    => 60002,
        ]);
        self::assertNull((new VideoStream($d))->getFps(1));

        $d = array_merge($data, [
            'r_frame_rate' => null,
            'duration'     => null,
            'nb_frames'    => null,
        ]);
        self::assertNull((new VideoStream($d))->getFps(1));

        // Test bug with some smartphone (i.e galaxy s7 reporting 9000 frames/seconds

        $d = array_merge($data, [
            'r_frame_rate' => '9000/1',
            'duration'     => 1,
            'nb_frames'    => 30,
        ]);
        self::assertEquals(30, (new VideoStream($d))->getFps(0));
    }
}
