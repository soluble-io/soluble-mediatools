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
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\ThumbParams;
use Soluble\MediaTools\Video\ThumbService;

class VideoThumbErrorLoggingTest extends TestCase
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

    public function testThumbProcessFailedMustBeLoggedAsError(): void
    {
        $outputFile = "{$this->outputDir}/tmp.jpg";
        $videoThumb = new ThumbService(new FFMpegConfig(), $this->logger);
        try {
            $videoThumb->makeThumbnail(
                $this->videoFile,
                $outputFile,
                (new ThumbParams())->withSeekTime(new SeekTime(10000000000000))
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::ERROR, mb_strtolower($logMsgs[0]['level_name']));
            self::assertRegExp(
                '/^Video thumbnailing failed (.*)ProcessFailedException(.*)"/',
                $logMsgs[0]['message']
            );
        }
    }

    public function testConversionMissingInputFileMustBeLoggedAsWarning(): void
    {
        $outputFile = "{$this->outputDir}/testConversionLoggingError.tmp.mp4";
        $videoThumb = new ThumbService(new FFMpegConfig(), $this->logger);
        try {
            $videoThumb->makeThumbnail(
                '/path_does_no_exists/path',
                $outputFile,
                (new ThumbParams())->withSeekTime(new SeekTime(0.0))
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::WARNING, mb_strtolower($logMsgs[0]['level_name']));
            self::assertRegExp(
                '/^Video thumbnailing failed (.*)MissingInputFile(.*)"/',
                $logMsgs[0]['message']
            );
        }
    }
}
