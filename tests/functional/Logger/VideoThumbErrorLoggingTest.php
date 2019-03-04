<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Functional\Logger;

use MediaToolsTest\Util\ServicesProviderTrait;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoThumbGenerator;
use Soluble\MediaTools\Video\VideoThumbParams;

class VideoThumbErrorLoggingTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var Logger */
    protected $logger;

    /** @var TestHandler */
    protected $loggerTestHandler;

    /** @var string */
    protected $loggerName;

    /** @var FFMpegConfigInterface */
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

        $this->ffmpegConfig = $this->getFFMpegConfig();

        $this->baseDir   = dirname(__FILE__, 3);
        $this->outputDir = "{$this->baseDir}/tmp";
        $this->videoFile = "{$this->baseDir}/data/big_buck_bunny_low.m4v";
    }

    public function testThumbProcessFailedMustBeLoggedAsError(): void
    {
        $outputFile = "{$this->outputDir}/tmp.jpg";
        $videoThumb = new VideoThumbGenerator($this->ffmpegConfig, $this->logger);
        try {
            $videoThumb->makeThumbnail(
                $this->videoFile,
                $outputFile,
                (new VideoThumbParams())->withSeekTime(new SeekTime(10000000000000))
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::ERROR, mb_strtolower($logMsgs[0]['level_name']));
            self::assertRegExp(
                '/^VideoThumbGenerator(.*)ProcessFailedException(.*)/',
                $logMsgs[0]['message']
            );
        }
    }

    public function testConversionMissingInputFileMustBeLoggedAsWarning(): void
    {
        $outputFile = "{$this->outputDir}/testConversionLoggingError.tmp.mp4";
        $videoThumb = new VideoThumbGenerator($this->ffmpegConfig, $this->logger);
        try {
            $videoThumb->makeThumbnail(
                '/path_does_no_exists/path',
                $outputFile,
                (new VideoThumbParams())->withSeekTime(new SeekTime(0.0))
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::WARNING, mb_strtolower($logMsgs[0]['level_name']));
            self::assertRegExp(
                '/^VideoThumbGenerator(.*)MissingInputFile(.*)/',
                $logMsgs[0]['message']
            );
        }
    }
}
