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
use Soluble\MediaTools\Video\Info\AudioStream;

class AudioStreamTest extends TestCase
{
    use FFProbeMetadataProviderTrait;

    public function setUp(): void
    {
    }

    public function testGetAudioStreams(): void
    {
        $d      = $this->getExampleFFProbeData()['streams'][2];
        $stream = new AudioStream($d);

        self::assertEquals($d['index'], $stream->getIndex());

        self::assertEquals($d['bit_rate'], $stream->getBitRate());
        self::assertEquals($d['codec_long_name'], $stream->getCodecLongName());
        self::assertEquals($d['codec_name'], $stream->getCodecName());
        self::assertEquals($d['codec_tag_string'], $stream->getCodecTagString());
        self::assertEquals($d['codec_time_base'], $stream->getCodecTimeBase());

        self::assertEquals(0, $stream->getStartTime());
        self::assertEquals('LC', $stream->getProfile());
        self::assertEquals($d['duration'], $stream->getDuration());
        self::assertEquals($d['duration_ts'], $stream->getDurationTs());

        self::assertEquals($d['tags'], $stream->getTags());
        self::assertEquals($d['time_base'], $stream->getTimeBase());

        self::assertEquals($d, $stream->getStreamMetadata());
    }
}
