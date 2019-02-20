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

        $d = $this->getExampleFFProbeData()['streams'][0];
        $stream = new VideoStream($d);

        self::assertEquals($d['index'], $stream->getIndex());

        self::assertEquals($d['nb_frames'], $stream->getNbFrames());
        self::assertEquals($d['width'], $stream->getWidth());
        self::assertEquals($d['avg_frame_rate'], $stream->getAvgFrameRate());
        self::assertEquals($d['bit_rate'], $stream->getBitRate());
        self::assertEquals($d['codec_long_name'], $stream->getCodecLongName());
        self::assertEquals($d['codec_name'], $stream->getCodecName());
        self::assertEquals($d['codec_tag_string'], $stream->getCodecTagString());
        self::assertEquals($d['codec_time_base'], $stream->getCodecTimeBase());

        self::assertEquals($d['coded_height'], $stream->getCodedHeight());
        self::assertEquals($d['coded_width'], $stream->getCodedWidth());
        self::assertEquals($d['color_range'], $stream->getColorRange());
        self::assertEquals($d['color_space'], $stream->getColorSpace());
        self::assertEquals($d['color_transfer'], $stream->getColorTransfer());

        self::assertEquals($d['display_aspect_ratio'], $stream->getDisplayAspectRatio());
        self::assertEquals($d['duration'], $stream->getDuration());
        self::assertEquals($d['duration_ts'], $stream->getDurationTs());
        self::assertEquals($d['height'], $stream->getHeight());
        self::assertEquals($d['width'], $stream->getWidth());

        self::assertEquals($d['level'], $stream->getLevel());
        self::assertEquals($d['pix_fmt'], $stream->getPixFmt());
        self::assertEquals($d['r_frame_rate'], $stream->getRFrameRate());

        self::assertEquals($d['sample_aspect_ratio'], $stream->getSampleAspectRatio());
        self::assertEquals($d['tags'], $stream->getTags());
        self::assertEquals($d['time_base'], $stream->getTimeBase());
        self::assertEquals($d['is_avc'] === 'true', $stream->isAvc());
    }

    public function testNullIsAvc(): void {

        $data = $this->getExampleFFProbeData()['streams'][0];
        unset($data['is_avc']);
        $stream = new VideoStream($data);
        self::assertNull($stream->isAvc());
    }


}
