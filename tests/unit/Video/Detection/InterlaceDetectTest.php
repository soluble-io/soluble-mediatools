<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Detection;

use MediaToolsTest\Util\FFProbeMetadataProviderTrait;
use MediaToolsTest\Util\ServicesProviderTrait;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Detection\InterlaceDetect;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;

class InterlaceDetectTest extends TestCase
{
    use FFProbeMetadataProviderTrait;

    use ServicesProviderTrait;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $vfsRoot;

    /** @var \org\bovigo\vfs\vfsStreamFile */
    private $virtualFile;

    /** @var InterlaceDetect */
    private $interlaceDetect;

    public function setUp(): void
    {
        $this->vfsRoot     = vfsStream::setup();
        $this->virtualFile = vfsStream::newFile('mediatools-test-virtual-file')
            ->withContent('A fake file used by mediatools tests')
            ->at($this->vfsRoot);

        $this->interlaceDetect = new InterlaceDetect(new FFMpegConfig());
    }

    public function testGuessInterlacingThrowsMissingInputFileException(): void
    {
        $this->expectException(MissingInputFileException::class);
        $this->interlaceDetect->guessInterlacing('./unexistent_file.mp4', 200);
    }

    public function testGuessInterlacingThrowsProcessFailedException(): void
    {
        $this->expectException(ProcessFailedException::class);
        $this->interlaceDetect->guessInterlacing($this->getTestFile(), 200);
    }

    private function getTestFile(): string
    {
        return $this->virtualFile->url();
    }
}
