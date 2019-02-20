<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\FileNotReadableException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Common\Process\ProcessFactory;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\Exception\ConverterProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\InvalidParamException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\MissingTimeException;
use Soluble\MediaTools\Video\Exception\NoOutputGeneratedException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\ProcessSignaledException;
use Soluble\MediaTools\Video\Exception\ProcessTimedOutException;
use Soluble\MediaTools\Video\Exception\RuntimeReaderException;
use Soluble\MediaTools\Video\Filter\SelectFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class VideoThumbGenerator implements VideoThumbGeneratorInterface
{
    public const DEFAULT_QUALITY_SCALE = 2;

    use PathAssertionsTrait;

    /** @var FFMpegConfigInterface */
    private $ffmpegConfig;

    /** @var int */
    private $defaultQualityScale;

    /** @var LoggerInterface */
    private $logger;

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
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, VideoThumbParamsInterface $thumbParams, ?ProcessParamsInterface $processParams = null): Process
    {
        $adapter = $this->ffmpegConfig->getAdapter();

        $conversionParams = (new VideoConvertParams());

        if (!$thumbParams->hasParam(VideoThumbParamsInterface::PARAM_SEEK_TIME)
         && !$thumbParams->hasParam(VideoThumbParamsInterface::PARAM_WITH_FRAME)) {
            throw new MissingTimeException('Missing seekTime/time or frame selection parameter');
        }

        if ($thumbParams->hasParam(VideoThumbParamsInterface::PARAM_SEEK_TIME)) {
            // TIME params are separated from the rest, so we can inject them
            // before input file
            $timeParams = (new VideoConvertParams())->withSeekStart(
                $thumbParams->getParam(VideoThumbParamsInterface::PARAM_SEEK_TIME)
            );
        } else {
            $timeParams = null;
        }

        if ($adapter->getDefaultThreads() !== null) {
            $conversionParams = $conversionParams->withThreads($adapter->getDefaultThreads());
        }

        // Only one frame for thumbnails :)
        $conversionParams = $conversionParams
            ->withVideoFrames(1)
            ->withVideoFilter($this->getThumbFilters($thumbParams));

        if ($thumbParams->hasParam(VideoThumbParamsInterface::PARAM_QUALITY_SCALE)) {
            $conversionParams = $conversionParams->withVideoQualityScale(
                $thumbParams->getParam(VideoThumbParamsInterface::PARAM_QUALITY_SCALE)
            );
        } else {
            $conversionParams = $conversionParams->withVideoQualityScale(
                $this->defaultQualityScale
            );
        }

        $arguments = $adapter->getMappedConversionParams($conversionParams);

        $ffmpegCmd = $adapter->getCliCommand(
            $arguments,
            $videoFile,
            $thumbnailFile,
            $timeParams !== null ? $adapter->getMappedConversionParams($timeParams) : []
        );

        $pp = $processParams ?? $this->ffmpegConfig->getProcessParams();

        return (new ProcessFactory($ffmpegCmd, $pp))();
    }

    /**
     * @throws ConverterExceptionInterface        Base exception class for conversion exceptions
     * @throws ConverterProcessExceptionInterface Base exception class for process conversion exceptions
     * @throws MissingInputFileException
     * @throws MissingTimeException
     * @throws ProcessTimedOutException
     * @throws ProcessFailedException
     * @throws ProcessSignaledException
     * @throws RuntimeReaderException
     * @throws InvalidParamException
     * @throws NoOutputGeneratedException
     */
    public function makeThumbnail(string $videoFile, string $thumbnailFile, VideoThumbParamsInterface $thumbParams, ?callable $callback = null, ?ProcessParamsInterface $processParams = null): void
    {
        try {
            try {
                $this->ensureFileReadable($videoFile);

                $process = $this->getSymfonyProcess($videoFile, $thumbnailFile, $thumbParams, $processParams);
                $process->mustRun($callback);
            } catch (FileNotFoundException | FileNotReadableException $e) {
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
                throw new RuntimeReaderException($e->getMessage());
            }

            if (!file_exists($thumbnailFile) || filesize($thumbnailFile) === 0) {
                $stdErr        = array_filter(explode("\n", trim($process->getErrorOutput())));
                $lastErrorLine = count($stdErr) > 0 ? $stdErr[count($stdErr) - 1] : 'no error message';
                throw new NoOutputGeneratedException(sprintf(
                    'Thumbnail was not generated, probably an invalid time/frame selection (ffmpeg: %s)',
                    $lastErrorLine
                ));
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

    private function getThumbFilters(VideoThumbParamsInterface $thumbParams): VideoFilterChain
    {
        $videoFilters = new VideoFilterChain();

        // Let's choose a frame
        if ($thumbParams->hasParam(VideoThumbParamsInterface::PARAM_WITH_FRAME)) {
            $frame      = $thumbParams->getParam(VideoThumbParamsInterface::PARAM_WITH_FRAME);
            $expression = sprintf('eq(n\,%d)', max(0, $frame - 1));
            $videoFilters->addFilter(new SelectFilter($expression));
        }

        // Let's add the remaning filters
        if ($thumbParams->hasParam(VideoThumbParamsInterface::PARAM_VIDEO_FILTER)) {
            $videoFilters->addFilter(
                $thumbParams->getParam(VideoThumbParamsInterface::PARAM_VIDEO_FILTER)
            );
        }

        return $videoFilters;
    }
}
