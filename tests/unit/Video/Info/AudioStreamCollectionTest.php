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
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Exception\NoStreamException;
use Soluble\MediaTools\Video\Info\AudioStream;
use Soluble\MediaTools\Video\Info\AudioStreamCollection;
use Soluble\MediaTools\Video\VideoInfo;

class AudioStreamCollectionTest extends TestCase
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

    public function testAudioStreamsWithData(): void
    {
        $data = $this->getExampleFFProbeData();

        $vi = new VideoInfo($this->getTestFile(), $data);
        $md = $vi->getAudioStreamsMetadata();

        $coll = new AudioStreamCollection($md);
        self::assertEquals(1, $coll->count());
        self::assertInstanceOf(AudioStream::class, $coll->getFirst());

        foreach ($coll as $idx => $audioStream) {
            // @var AudioStream $audioStream
            self::assertEquals($coll->getIterator()[$idx], $audioStream);
            self::assertEquals('audio', $audioStream->getCodecType());
        }
    }

    public function testAudioStreamsWithDataThrowsException(): void
    {
        self::expectException(InvalidStreamMetadataException::class);
        new AudioStreamCollection([0 => 'cool']);
    }

    public function testAudioStreamsWithDataThrowsNoStreamException(): void
    {
        self::expectException(NoStreamException::class);
        (new AudioStreamCollection([]))->getFirst();
    }

    private function getTestFile(): string
    {
        return $this->virtualFile->url();
    }
}
