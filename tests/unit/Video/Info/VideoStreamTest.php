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
        }
    }

    public function testNullIsAvc(): void
    {
        $data = $this->getExampleFFProbeData()['streams'][0];
        unset($data['is_avc']);
        $stream = new VideoStream($data);
        self::assertNull($stream->isAvc());
    }
}
