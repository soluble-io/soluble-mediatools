<?php

declare(strict_types=1);

namespace MediaToolsTest\Recipes;

use MediaToolsTest\TestUtilTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Soluble\MediaTools\VideoConvertFactory;
use Soluble\MediaTools\VideoConvertParams;

class VideoConversionTest extends TestCase
{
    use TestUtilTrait;

    /** @var ContainerInterface */
    protected $container;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->container = $this->getConfiguredContainer();
    }

    public function testBasicTransmuxing(): void
    {
        $baseDir = dirname(__FILE__, 3);

        $videoTranscode = (new VideoConvertFactory())->__invoke($this->container);

        $inputFile  = $baseDir . '/data/big_buck_bunny_low.m4v';
        $outputFile = $baseDir . '/output/big_buck_bunny_low.output.mp4';

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $transcodeParams = (new VideoConvertParams())
            ->withVideoCodec('copy')
            ->withOutputFormat('mp4');

        self::assertFileExists($inputFile);
        self::assertFileNotExists($outputFile);

        $process = $videoTranscode->convert($inputFile, $outputFile, $transcodeParams);

        if (!$process->isSuccessful()) {
            @unlink($outputFile);
            self::fail(
                sprintf(
                    "Command '%s' failed with error code '%s', error output: '%s' / '%s'.",
                    $process->getCommandLine(),
                    $process->getExitCode(),
                    $process->getErrorOutput(),
                    $process->getOutput()
                )
            );
        }

        self::assertEquals('', $process->getOutput());
        self::assertGreaterThan(0, mb_strlen($process->getErrorOutput()));
        self::assertFileExists($outputFile);
        unlink($outputFile);
    }

    public function testConversionWithVideoFilter(): void
    {
    }
}
