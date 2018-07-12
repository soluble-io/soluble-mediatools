<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Common\Process\ProcessFactory;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Exception\ConversionExceptionInterface;
use Soluble\MediaTools\Video\Exception\ConversionProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\InvalidParamException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\MissingTimeException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\ProcessSignaledException;
use Soluble\MediaTools\Video\Exception\ProcessTimedOutException;
use Soluble\MediaTools\Video\Exception\RuntimeException;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class ThumbService implements ThumbServiceInterface
{
    public const DEFAULT_QUALITY_SCALE = 2;

    use PathAssertionsTrait;

    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    /** @var int */
    protected $defaultQualityScale;

    /** @var LoggerInterface|NullLogger */
    protected $logger;

    public function __construct(FFMpegConfigInterface $ffmpegConfig, ?LoggerInterface $logger = null, int $defaultQualityScale = self::DEFAULT_QUALITY_SCALE)
    {
        $this->ffmpegConfig        = $ffmpegConfig;
        $this->defaultQualityScale = $defaultQualityScale;
        $this->logger              = $logger ?? new NullLogger();
    }

    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want
     * handle the process your way...
     *
     * @see https://symfony.com/doc/current/components/process.html
     *
     * @throws UnsupportedParamException
     * @throws UnsupportedParamValueException
     * @throws MissingTimeException
     */
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, ThumbParamsInterface $thumbParams, ?ProcessParamsInterface $processParams = null): Process
    {
        $adapter = $this->ffmpegConfig->getAdapter();

        if (!$thumbParams->hasParam(ThumbParamsInterface::PARAM_SEEK_TIME)) {
            throw new MissingTimeException('Missing seekTime parameter');
        }

        $conversionParams = (new ConversionParams());

        if ($adapter->getDefaultThreads() !== null) {
            $conversionParams->withThreads($adapter->getDefaultThreads());
        }

        // TIME must be the first !!!

        $conversionParams = $conversionParams->withSeekStart(
            $thumbParams->getParam(ThumbParamsInterface::PARAM_SEEK_TIME)
        );

        // Only one frame
        $conversionParams = $conversionParams->withVideoFrames(1);

        if ($thumbParams->hasParam(ThumbParamsInterface::PARAM_VIDEO_FILTER)) {
            $conversionParams = $conversionParams->withVideoFilter(
                $thumbParams->getParam(ThumbParamsInterface::PARAM_VIDEO_FILTER)
            );
        }

        if ($thumbParams->hasParam(ThumbParamsInterface::PARAM_QUALITY_SCALE)) {
            $conversionParams = $conversionParams->withVideoQualityScale(
                $thumbParams->getParam(ThumbParamsInterface::PARAM_QUALITY_SCALE)
            );
        } else {
            $conversionParams = $conversionParams->withVideoQualityScale(
                $this->defaultQualityScale
            );
        }

        $arguments = $adapter->getMappedConversionParams($conversionParams);
        $ffmpegCmd = $adapter->getCliCommand($arguments, $videoFile, $thumbnailFile);

        $pp = $processParams ?? $this->ffmpegConfig->getProcessParams();

        return (new ProcessFactory($ffmpegCmd, $pp))();
    }

    /**
     * @throws ConversionExceptionInterface        Base exception class for conversion exceptions
     * @throws ConversionProcessExceptionInterface Base exception class for process conversion exceptions
     * @throws MissingInputFileException
     * @throws MissingTimeException
     * @throws ProcessTimedOutException
     * @throws ProcessFailedException
     * @throws ProcessSignaledException
     * @throws RuntimeException
     * @throws InvalidParamException
     */
    public function makeThumbnail(string $videoFile, string $thumbnailFile, ThumbParamsInterface $thumbParams, ?callable $callback = null, ?ProcessParamsInterface $processParams = null): void
    {
        try {
            try {
                $this->ensureFileExists($videoFile);

                $process = $this->getSymfonyProcess($videoFile, $thumbnailFile, $thumbParams, $processParams);
                $process->mustRun($callback);
            } catch (FileNotFoundException $e) {
                throw new MissingInputFileException($e->getMessage());
            } catch (UnsupportedParamValueException | UnsupportedParamException $e) {
                throw new InvalidParamException($e->getMessage());
            } catch (SPException\ProcessTimedOutException $e) {
                throw new ProcessTimedOutException($e->getProcess(), $e);
            } catch (SPException\ProcessSignaledException $e) {
                throw new ProcessSignaledException($e->getProcess(), $e);
            } catch (SPException\ProcessFailedException $e) {
                throw new ProcessFailedException($e->getProcess(), $e);
            } catch (SPException\RuntimeException $e) {
                throw new RuntimeException($e->getMessage());
            }
        } catch (\Throwable $e) {
            $exceptionNs = explode('\\', get_class($e));
            $this->logger->log(
                ($e instanceof MissingInputFileException) ? LogLevel::WARNING : LogLevel::ERROR,
                sprintf(
                    'Video thumbnailing failed \'%s\' with \'%s\'. "%s(%s, %s,...)"',
                    $exceptionNs[count($exceptionNs) - 1],
                    __METHOD__,
                    $e->getMessage(),
                    $videoFile,
                    $thumbnailFile
                )
            );
            throw $e;
        }
    }
}
