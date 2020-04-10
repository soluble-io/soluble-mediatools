<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Info;

use MediaToolsTest\Util\FFProbeMetadataProviderTrait;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Exception\NoStreamException;
use Soluble\MediaTools\Video\Info\SubtitleStream;
use Soluble\MediaTools\Video\Info\SubtitleStreamCollection;
use Soluble\MediaTools\Video\VideoInfo;

class SubtitleStreamCollectionTest extends TestCase
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

    public function testSutitleStreamsWithData(): void
    {
        $data = $this->getExampleFFProbeData();

        $vi = new VideoInfo($this->getTestFile(), $data);
        $md = $vi->getStreamsMetadataByType(VideoInfo::STREAM_TYPE_SUBTITLE);

        $coll = new SubtitleStreamCollection($md);
        self::assertEquals(1, $coll->count());
        self::assertInstanceOf(SubtitleStream::class, $coll->getFirst());

        foreach ($coll as $idx => $stream) {
            // @var SubtitleStream $stream
            self::assertEquals($coll->getIterator()[$idx], $stream);
            self::assertEquals('subtitle', $stream->getCodecType());
        }
    }

    public function testSubtitleStreamsWithDataThrowsException(): void
    {
        $this->expectException(InvalidStreamMetadataException::class);
        new SubtitleStreamCollection([0 => 'cool']);
    }

    public function testSubtitleStreamsWithDataThrowsNoStreamException(): void
    {
        $this->expectException(NoStreamException::class);
        (new SubtitleStreamCollection([]))->getFirst();
    }

    private function getTestFile(): string
    {
        return $this->virtualFile->url();
    }
}
