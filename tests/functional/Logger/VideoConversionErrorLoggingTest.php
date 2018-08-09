<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

namespace MediaToolsTest\Functional\Logger;

use MediaToolsTest\Util\ServicesProviderTrait;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\ConversionParams;
use Soluble\MediaTools\Video\VideoConverter;

class VideoConversionErrorLoggingTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var Logger */
    protected $logger;

    /** @var TestHandler */
    protected $loggerTestHandler;

    /** @var string */
    protected $loggerName;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $outputDir;

    /** @var string */
    protected $videoFile;

    public function setUp(): void
    {
        $this->loggerName        = '[soluble-mediatools]';
        $this->logger            = new Logger($this->loggerName);
        $this->loggerTestHandler = new TestHandler(Logger::DEBUG);
        $this->logger->pushHandler($this->loggerTestHandler);

        $this->ffmpegConfig = $this->getConfiguredContainer()->get(FFMpegConfig::class);

        $this->baseDir      = dirname(__FILE__, 3);
        $this->outputDir    = "{$this->baseDir}/tmp";
        $this->videoFile    = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testConversionProcessFailedMustBeLoggedAsError(): void
    {
        $outputFile   = "{$this->outputDir}/testConversionLoggingError.tmp.mp4";
        $videoConvert = new VideoConverter(new FFMpegConfig(), $this->logger);
        try {
            $videoConvert->convert(
                $this->videoFile,
                $outputFile,
                (new ConversionParams())->withVideoCodec('NOVALIDCODEC')
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::ERROR, mb_strtolower($logMsgs[0]['level_name']));
            self::assertRegExp(
                '/^Video conversion failed(.*)ProcessFailedException(.*)"/',
                $logMsgs[0]['message']
            );
        }
    }

    public function testConversionMissingInputFileMustBeLoggedAsWarning(): void
    {
        $outputFile   = "{$this->outputDir}/testConversionLoggingError.tmp.mp4";
        $videoConvert = new VideoConverter(new FFMpegConfig(), $this->logger);
        try {
            $videoConvert->convert(
                '/path_does_no_exists/path',
                $outputFile,
                (new ConversionParams())
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::WARNING, mb_strtolower($logMsgs[0]['level_name']));
            self::assertRegExp(
                '/^Video conversion failed(.*)MissingInputFile(.*)"/',
                $logMsgs[0]['message']
            );
        }
    }
}
