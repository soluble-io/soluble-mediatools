<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Functional\Logger;

use MediaToolsTest\Util\PhpUnitPolyfillTrait;
use MediaToolsTest\Util\ServicesProviderTrait;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\VideoConverter;
use Soluble\MediaTools\Video\VideoConvertParams;

class VideoConversionErrorLoggingTest extends TestCase
{
    use ServicesProviderTrait;

    use PhpUnitPolyfillTrait;

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

    public function testConversionProcessFailedMustBeLoggedAsError(): void
    {
        $outputFile   = "{$this->outputDir}/testConversionLoggingError.tmp.mp4";
        $videoConvert = new VideoConverter($this->getFFMpegConfig(), $this->logger);

        try {
            $videoConvert->convert(
                $this->videoFile,
                $outputFile,
                (new VideoConvertParams())->withVideoCodec('NOVALIDCODEC')
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::ERROR, mb_strtolower($logMsgs[0]['level_name']));
            self::assertMatchesRegularExpressionPolyfilled(
                '/^VideoConverter(.*)ProcessFailedException(.*)/',
                $logMsgs[0]['message']
            );
        }
    }

    public function testConversionMissingInputFileMustBeLoggedAsWarning(): void
    {
        $outputFile   = "{$this->outputDir}/testConversionLoggingError.tmp.mp4";
        $videoConvert = new VideoConverter($this->getFFMpegConfig(), $this->logger);

        try {
            $videoConvert->convert(
                '/path_does_no_exists/path',
                $outputFile,
                (new VideoConvertParams())
            );
            self::fail('this code cannot be reached');
        } catch (\Throwable $e) {
            $logMsgs = $this->loggerTestHandler->getRecords() ?? [];
            self::assertCount(1, $logMsgs);
            self::assertEquals(LogLevel::WARNING, mb_strtolower($logMsgs[0]['level_name']));
            self::assertMatchesRegularExpressionPolyfilled(
                '/^VideoConverter(.*)MissingInputFile(.*)/',
                $logMsgs[0]['message']
            );
        }
    }
}
