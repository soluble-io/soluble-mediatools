<?php

declare(strict_types=1);

namespace MediaToolsTest\Recipes;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\ConfigProvider;
use Soluble\MediaTools\VideoTranscodeFactory;
use Soluble\MediaTools\VideoTranscodeParams;
use Zend\ServiceManager\ServiceManager;

class BasicVideoTranscodeTest extends TestCase
{
    /** @var ContainerInterface */
    protected $container;

    public function setUp(): void
    {
        $this->container = $this->getTestsContainer();
    }

    public function getTestsContainer(): ContainerInterface
    {
        return new ServiceManager(
            array_merge(
                [
                    'services' => [
                        'config' => [
                            'soluble-mediatools' => [
                                'ffmpeg.binary'  => 'ffmpeg',
                                'ffprobe.binary' => 'ffprobe',
                            ],
                        ],
                    ]],
                (new ConfigProvider())->getDependencies()
            )
        );
    }

    public function testBasicTranscode(): void
    {
        $baseDir = dirname(__FILE__, 3);

        $videoTranscode = (new VideoTranscodeFactory())->__invoke($this->container);

        $inputFile  = $baseDir . '/data/big_buck_bunny_low.m4v';
        $outputFile = $baseDir . '/output/big_buck_bunny_low.output.mp4';

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $transcodeParams = (new VideoTranscodeParams())
            ->withVideoCodec('copy')
            ->withOutputFormat('mp4');

        $process = $videoTranscode->transcode($inputFile, $outputFile, $transcodeParams);

        $stdOut = $stdErr = '';
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $stdOut .= $data;
            } else { // $process::ERR === $type
                $stdErr .= $data;
            }
        }

        self::assertEquals(0, $process->getExitCode());
        self::assertEquals('', $stdOut);
        self::assertGreaterThan(0, mb_strlen($stdErr));

        unlink($outputFile);
    }
}
