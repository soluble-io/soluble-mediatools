<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use MediaToolsTest\Util\FFProbeMetadataProviderTrait;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\IOException;
use Soluble\MediaTools\Common\Exception\JsonParseException;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Info\AudioStream;
use Soluble\MediaTools\Video\Info\SubtitleStream;
use Soluble\MediaTools\Video\Info\VideoStream;
use Soluble\MediaTools\Video\VideoInfo;

class VideoInfoTest extends TestCase
{
    use FFProbeMetadataProviderTrait;

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
        new VideoInfo('/unexistent_file/path/file', $this->getExampleFFProbeData());
    }

    public function testGetFileMethods(): void
    {
        $vi = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());

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
            $vi = new VideoInfo($tmpFile, $this->getExampleFFProbeData());
        } catch (\Throwable $e) {
            unlink($tmpFile);
            throw new \RuntimeException(sprintf('Cannot run test %s', $e->getMessage()));
        }
        unlink($tmpFile);
        $vi->getFileSize();
    }

    public function testCountStreams(): void
    {
        $vi = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        self::assertEquals(4, $vi->countStreams());

        self::assertEquals(2, $vi->countStreams(VideoInfo::STREAM_TYPE_VIDEO));
        self::assertEquals(1, $vi->countStreams(VideoInfo::STREAM_TYPE_AUDIO));
        self::assertEquals(0, $vi->countStreams(VideoInfo::STREAM_TYPE_DATA));
        self::assertEquals(1, $vi->countStreams(VideoInfo::STREAM_TYPE_SUBTITLE));
    }

    public function testGetVideoStreams(): void
    {
        $vi      = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        $streams = $vi->getVideoStreams();
        self::assertEquals(2, $streams->count());
        /**
         * @var VideoStream $stream
         */
        foreach ($streams as $idx => $stream) {
            self::assertInstanceOf(VideoStream::class, $stream);
            self::assertEquals($idx, $stream->getIndex());
        }
    }

    public function testGetSubtitleStreams(): void
    {
        $vi      = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        $streams = $vi->getSubtitleStreams();
        self::assertEquals(1, $streams->count());
        /**
         * @var SubtitleStream $stream
         */
        foreach ($streams as $idx => $stream) {
            self::assertInstanceOf(SubtitleStream::class, $stream);
            self::assertEquals($idx, $stream->getIndex());
        }
    }

    public function testGetAudioStreams(): void
    {
        $vi      = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        $streams = $vi->getAudioStreams();
        self::assertEquals(1, $streams->count());
        /**
         * @var AudioStream $stream
         */
        foreach ($streams as $idx => $stream) {
            self::assertInstanceOf(AudioStream::class, $stream);
            //self::assertEquals($idx, $stream->getIndex());
        }
    }

    public function testGetAudioStreamsThrowsException(): void
    {
        self::expectException(InvalidStreamMetadataException::class);
        $vi = new VideoInfo($this->getTestFile(), ['streams' => [0 => 'cool']]);
        $vi->getAudioStreams();
    }

    public function testGetVideoStreamsThrowsException(): void
    {
        self::expectException(InvalidStreamMetadataException::class);
        $vi = new VideoInfo($this->getTestFile(), ['streams' => [0 => 'cool']]);
        $vi->getVideoStreams();
    }

    public function testGetSubtitleStreamsThrowsException(): void
    {
        self::expectException(InvalidStreamMetadataException::class);
        $vi = new VideoInfo($this->getTestFile(), ['streams' => [0 => 'cool']]);
        $vi->getSubtitleStreams();
    }

    public function testGetMetadata(): void
    {
        $vi = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        self::assertSame(self::getExampleFFProbeData(), $vi->getMetadata());
    }

    public function testGetFormatName(): void
    {
        $vi = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        self::assertEquals('mov,mp4,m4a,3gp,3g2,mj2', $vi->getFormatName());
    }

    public function testGetStreamMetadataByType(): void
    {
        $data  = $this->getExampleFFProbeData();
        $vi    = new VideoInfo($this->getTestFile(), $data);
        $audio = $vi->getStreamsMetadataByType(VideoInfo::STREAM_TYPE_AUDIO);
        self::assertEquals($data['streams'][2], $audio[0]);

        $video = $vi->getStreamsMetadataByType(VideoInfo::STREAM_TYPE_VIDEO);
        self::assertEquals($data['streams'][0], $video[0]);

        $data = $vi->getStreamsMetadataByType(VideoInfo::STREAM_TYPE_DATA);
        self::assertEquals([], $data);
    }

    public function testGetStreamMetadataByTypeThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);
        $vi = new VideoInfo($this->getTestFile(), $this->getExampleFFProbeData());
        $vi->getStreamsMetadataByType('unsupported');
    }

    public function testGetStreamMetadataByTypeThrowsInvalidStreamMetadataException1(): void
    {
        self::expectException(InvalidStreamMetadataException::class);
        $vi = new VideoInfo($this->getTestFile(), ['streams' => [0 => []]]);
        $vi->getStreamsMetadataByType(VideoInfo::STREAM_TYPE_VIDEO);
    }

    public function testGetStreamMetadataByTypeThrowsInvalidStreamMetadataException2(): void
    {
        self::expectException(InvalidStreamMetadataException::class);
        $vi = new VideoInfo($this->getTestFile(), ['invalid' => []]);
        $vi->getStreamsMetadataByType(VideoInfo::STREAM_TYPE_VIDEO);
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
}
