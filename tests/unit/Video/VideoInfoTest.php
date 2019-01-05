<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\IOException;
use Soluble\MediaTools\Common\Exception\JsonParseException;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\VideoInfo;

class VideoInfoTest extends TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $vfsRoot;

    /** @var \org\bovigo\vfs\vfsStreamFile */
    private $virtualFile;

    public function setUp(): void
    {
        $this->vfsRoot     = vfsStream::setup();
        $this->virtualFile = vfsStream::newFile('mediatools-test-virtual-file')
            ->withContent('A fake file used by mediatools tests')
            ->at($this->vfsRoot);
    }

    public function testConstructThrowsIOException(): void
    {
        self::expectException(IOException::class);
        new VideoInfo('/unexistent_file/path/file', self::getExampleFFProbeData());
    }

    public function testGetFileMethods(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());

        self::assertSame($this->getTestFile(), $vi->getFile());
        self::assertSame(filesize($this->getTestFile()), $vi->getFileSize());
    }

    public function testFileSizeThrowsIOException(): void
    {
        self::expectException(IOException::class);
        $tmpFile = tempnam(sys_get_temp_dir(), 'testFileSizeThrowsIOException.tmp');
        if (!is_string($tmpFile)) {
            throw new \RuntimeException('VideoInfoTest: Cannot create temp file');
        }
        try {
            $vi = new VideoInfo($tmpFile, self::getExampleFFProbeData());
        } catch (\Throwable $e) {
            unlink($tmpFile);
            throw new \RuntimeException(sprintf('Cannot run test %s', $e->getMessage()));
        }
        unlink($tmpFile);
        $vi->getFileSize();
    }

    public function testCountStreams(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertEquals(2, $vi->countStreams());

        self::assertEquals(1, $vi->countStreams(VideoInfo::STREAM_TYPE_VIDEO));
        self::assertEquals(1, $vi->countStreams(VideoInfo::STREAM_TYPE_AUDIO));
        self::assertEquals(0, $vi->countStreams(VideoInfo::STREAM_TYPE_DATA));
    }

    public function testGetDimensions(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertSame(['width' => 320, 'height' => 180], $vi->getDimensions());
    }

    public function testGetWidthAndHeight(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertEquals(320, $vi->getWidth());
        self::assertEquals(180, $vi->getHeight());

        // with stream that does not exists
        self::assertEquals(0, $vi->getHeight(2));
        self::assertEquals(0, $vi->getWidth(2));
    }

    public function testGetMetadata(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertSame(self::getExampleFFProbeData(), $vi->getMetadata());
    }

    public function testGetAudioVideoBitRate(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertEquals(39933, $vi->getVideoBitrate());
        self::assertEquals(84255, $vi->getAudioBitrate());

        // with streams that does not exists
        self::assertEquals(0, $vi->getVideoBitrate(2));
        self::assertEquals(0, $vi->getAudioBitrate(2));
    }

    public function testGetAudioVideoCodecName(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertEquals('h264', $vi->getVideoCodecName());
        self::assertEquals('aac', $vi->getAudioCodecName());

        // with streams that does not exists
        self::assertNull($vi->getVideoCodecName(2));
        self::assertNull($vi->getAudioCodecName(2));
    }

    public function testGetFormatName(): void
    {
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        self::assertEquals('mov,mp4,m4a,3gp,3g2,mj2', $vi->getFormatName());
    }

    public function testGetStreamMetadataByTypeThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);
        $vi = new VideoInfo($this->getTestFile(), self::getExampleFFProbeData());
        $vi->getStreamsMetadataByType('unsupported');
    }

    public function testCreateFromFFProbeJsonThrowsJsonExceptionWhenEmpty(): void
    {
        self::expectException(JsonParseException::class);
        VideoInfo::createFromFFProbeJson($this->getTestFile(), '');
    }

    public function testCreateFromFFProbeJsonThrowsJsonExceptionWhenInvalid(): void
    {
        self::expectException(JsonParseException::class);
        VideoInfo::createFromFFProbeJson($this->getTestFile(), '{test: aa}');
    }

    private function getTestFile(): string
    {
        return $this->virtualFile->url();
    }

    /**
     * Return example of ffprobe result.
     *
     * @return array
     */
    public static function getExampleFFProbeData(): array
    {
        return [
            'streams' => [
                0 => [
                    'index'                => 0,
                    'codec_name'           => 'h264',
                    'codec_long_name'      => 'H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10',
                    'profile'              => 'Main',
                    'codec_type'           => 'video',
                    'codec_time_base'      => '81/2968',
                    'codec_tag_string'     => 'avc1',
                    'codec_tag'            => '0x31637661',
                    'width'                => 320,
                    'height'               => 180,
                    'coded_width'          => 320,
                    'coded_height'         => 180,
                    'has_b_frames'         => 2,
                    'sample_aspect_ratio'  => '1:1',
                    'display_aspect_ratio' => '16:9',
                    'pix_fmt'              => 'yuv420p',
                    'level'                => 40,
                    'color_range'          => 'tv',
                    'color_space'          => 'smpte170m',
                    'color_transfer'       => 'bt709',
                    'color_primaries'      => 'smpte170m',
                    'chroma_location'      => 'left',
                    'refs'                 => 1,
                    'is_avc'               => 'true',
                    'nal_length_size'      => '4',
                    'r_frame_rate'         => '120/1',
                    'avg_frame_rate'       => '1484/81',
                    'time_base'            => '1/90000',
                    'start_pts'            => 0,
                    'start_time'           => '0.000000',
                    'duration_ts'          => 5467500,
                    'duration'             => '60.750000',
                    'bit_rate'             => '39933',
                    'bits_per_raw_sample'  => '8',
                    'nb_frames'            => '1113',
                    'disposition'          => [
                        'default'          => 1,
                        'dub'              => 0,
                        'original'         => 0,
                        'comment'          => 0,
                        'lyrics'           => 0,
                        'karaoke'          => 0,
                        'forced'           => 0,
                        'hearing_impaired' => 0,
                        'visual_impaired'  => 0,
                        'clean_effects'    => 0,
                        'attached_pic'     => 0,
                        'timed_thumbnails' => 0,
                    ],
                    'tags' => [
                        'creation_time' => '2018-07-04T14:51:24.000000Z',
                        'language'      => 'und',
                        'handler_name'  => 'VideoHandler',
                    ],
                ],
                1 => [
                    'index'            => 1,
                    'codec_name'       => 'aac',
                    'codec_long_name'  => 'AAC (Advanced Audio Coding)',
                    'profile'          => 'LC',
                    'codec_type'       => 'audio',
                    'codec_time_base'  => '1/22050',
                    'codec_tag_string' => 'mp4a',
                    'codec_tag'        => '0x6134706d',
                    'sample_fmt'       => 'fltp',
                    'sample_rate'      => '22050',
                    'channels'         => 1,
                    'channel_layout'   => 'mono',
                    'bits_per_sample'  => 0,
                    'r_frame_rate'     => '0/0',
                    'avg_frame_rate'   => '0/0',
                    'time_base'        => '1/22050',
                    'start_pts'        => 0,
                    'start_time'       => '0.000000',
                    'duration_ts'      => 1355766,
                    'duration'         => '61.485986',
                    'bit_rate'         => '84255',
                    'max_bit_rate'     => '84255',
                    'nb_frames'        => '1325',
                    'disposition'      => [
                        'default'          => 1,
                        'dub'              => 0,
                        'original'         => 0,
                        'comment'          => 0,
                        'lyrics'           => 0,
                        'karaoke'          => 0,
                        'forced'           => 0,
                        'hearing_impaired' => 0,
                        'visual_impaired'  => 0,
                        'clean_effects'    => 0,
                        'attached_pic'     => 0,
                        'timed_thumbnails' => 0,
                    ],
                    'tags' => [
                        'creation_time' => '2018-07-04T14:51:24.000000Z',
                        'language'      => 'eng',
                        'handler_name'  => 'Mono',
                    ],
                ],
            ],
            'format' => [
                'filename'         => '/tmp/big_buck_bunny_low.m4v',
                'nb_streams'       => 2,
                'nb_programs'      => 0,
                'format_name'      => 'mov,mp4,m4a,3gp,3g2,mj2',
                'format_long_name' => 'QuickTime / MOV',
                'start_time'       => '0.000000',
                'duration'         => '61.533000',
                'size'             => '983115',
                'bit_rate'         => '127816',
                'probe_score'      => 100,
                'tags'             => [
                    'major_brand'       => 'mp42',
                    'minor_version'     => '512',
                    'compatible_brands' => 'isomiso2avc1mp41',
                    'creation_time'     => '2018-07-04T14:51:24.000000Z',
                    'title'             => 'big_buck_bunny',
                    'encoder'           => 'HandBrake 1.1.0 2018042400',
                ],
            ],
        ];
    }
}
