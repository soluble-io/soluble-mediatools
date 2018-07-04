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
        if (!defined('FFMPEG_BINARY_PATH')) {
            throw new \Exception('Missing phpunit constant FFMPEG_BINARY_PATH');
        }
        if (!defined('FFPROBE_BINARY_PATH')) {
            throw new \Exception('Missing phpunit constant FFPROBE_BINARY_PATH');
        }

        $ffmpegBinary  = realpath(FFMPEG_BINARY_PATH);
        $ffprobeBinary = realpath(FFPROBE_BINARY_PATH);

        if ($ffmpegBinary === false || !file_exists($ffmpegBinary)) {
            throw new \Exception(sprintf(
                'FFMPEG binary does not exists: %s, realpath returned: %s',
                FFMPEG_BINARY_PATH,
                is_bool($ffmpegBinary) ? 'false' : $ffmpegBinary
            ));
        }

        if ($ffprobeBinary === false || !file_exists($ffprobeBinary)) {
            throw new \Exception(sprintf(
                'FFPROBE binary does not exists: %s, realpath returned: %s',
                FFPROBE_BINARY_PATH,
                is_bool($ffprobeBinary) ? 'false' : $ffprobeBinary
            ));
        }

        $config = [
            'soluble-mediatools' => [
                'ffmpeg.binary'  => realpath(FFMPEG_BINARY_PATH),
                'ffprobe.binary' => realpath(FFPROBE_BINARY_PATH),
            ],
        ];

        return new ServiceManager(
            array_merge(
                [
                    'services' => [
                        'config' => $config,
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

        self::assertFileExists($inputFile);
        self::assertFileNotExists($outputFile);

        $process = $videoTranscode->transcode($inputFile, $outputFile, $transcodeParams);

        $stdOut = $stdErr = '';
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $stdOut .= $data;
                echo $data;
            } else { // $process::ERR === $type
                $stdErr .= $data;
                echo $data;
            }
        }

        $exitCode = $process->getExitCode();
        if ($exitCode !== 0) {
            var_dump($process->getErrorOutput());
        }

        self::assertEquals(0, $exitCode);
        self::assertEquals('', $stdOut);
        self::assertGreaterThan(0, mb_strlen($stdErr));
        self::assertFileExists($outputFile);
        unlink($outputFile);
    }
}
