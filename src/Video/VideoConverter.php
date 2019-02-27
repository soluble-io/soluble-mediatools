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
use Soluble\MediaTools\Common\Exception as CommonException;
use Soluble\MediaTools\Common\IO\UnescapedFileInterface;
use Soluble\MediaTools\Common\Process\ProcessFactory;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\Exception\ConverterProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\InvalidParamException;
use Soluble\MediaTools\Video\Exception\MissingFFMpegBinaryException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\ProcessSignaledException;
use Soluble\MediaTools\Video\Exception\ProcessTimedOutException;
use Soluble\MediaTools\Video\Exception\RuntimeReaderException;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class VideoConverter implements VideoConverterInterface
{
    use PathAssertionsTrait;

    /** @var FFMpegConfigInterface */
    private $ffmpegConfig;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(FFMpegConfigInterface $ffmpegConfig, ?LoggerInterface $logger = null)
    {
        $this->ffmpegConfig = $ffmpegConfig;

        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want to make
     * things async...
     *
     * @param null|string|UnescapedFileInterface $outputFile
     *
     * @see https://symfony.com/doc/current/components/process.html
     *
     * @throws CommonException\UnsupportedParamException
     * @throws CommonException\UnsupportedParamValueException
     * @throws InvalidArgumentException
     */
    public function getSymfonyProcess(string $inputFile, $outputFile, VideoConvertParamsInterface $convertParams, ?ProcessParamsInterface $processParams = null): Process
    {
        $adapter = $this->ffmpegConfig->getAdapter();

        if (!$convertParams->hasParam(VideoConvertParamsInterface::PARAM_THREADS)
            && $adapter->getDefaultThreads() !== null) {
            $convertParams = $convertParams->withBuiltInParam(
                VideoConvertParamsInterface::PARAM_THREADS,
                $adapter->getDefaultThreads()
            );
        }

        $arguments = $adapter->getMappedConversionParams($convertParams);

        try {
            $ffmpegCmd = $adapter->getCliCommand($arguments, $inputFile, $outputFile);
        } catch (CommonException\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), (int) $e->getCode(), $e);
        }

        $pp = $processParams ?? $this->ffmpegConfig->getProcessParams();

        return (new ProcessFactory($ffmpegCmd, $pp))();
    }

    /**
     * Run a conversion, throw exception on error.
     *
     * @param null|string|UnescapedFileInterface $outputFile
     * @param callable|null                      $callback   A PHP callback to run whenever there is some
     *                                                       tmp available on STDOUT or STDERR
     *
     * @throws ConverterExceptionInterface        Base exception class for conversion exceptions
     * @throws ConverterProcessExceptionInterface Base exception class for process conversion exceptions
     * @throws MissingInputFileException
     * @throws MissingFFMpegBinaryException
     * @throws ProcessTimedOutException
     * @throws ProcessFailedException
     * @throws ProcessSignaledException
     * @throws InvalidParamException
     * @throws RuntimeReaderException
     */
    public function convert(string $inputFile, $outputFile, VideoConvertParamsInterface $convertParams, ?callable $callback = null, ?ProcessParamsInterface $processParams = null): void
    {
        try {
            try {
                $this->ensureFileReadable($inputFile, true);
                $process = $this->getSymfonyProcess($inputFile, $outputFile, $convertParams, $processParams);
                $process->mustRun($callback);
            } catch (CommonException\FileNotFoundException | CommonException\FileNotReadableException | CommonException\FileEmptyException $e) {
                throw new MissingInputFileException($e->getMessage());
            } catch (CommonException\UnsupportedParamValueException | CommonException\UnsupportedParamException $e) {
                throw new InvalidParamException($e->getMessage());
            } catch (SPException\ProcessSignaledException $e) {
                throw new ProcessSignaledException($e->getProcess(), $e);
            } catch (SPException\ProcessTimedOutException $e) {
                throw new ProcessTimedOutException($e->getProcess(), $e);
            } catch (SPException\ProcessFailedException $e) {
                $process = $e->getProcess();
                if ($process->getExitCode() === 127 ||
                    mb_strpos(mb_strtolower($process->getExitCodeText()), 'command not found') !== false) {
                    throw new MissingFFMpegBinaryException($process, $e);
                }
                throw new ProcessFailedException($process, $e);
            } catch (SPException\RuntimeException $e) {
                throw new RuntimeReaderException($e->getMessage());
            }
        } catch (\Throwable $e) {
            $exceptionNs = explode('\\', get_class($e));
            $this->logger->log(
                ($e instanceof MissingInputFileException) ? LogLevel::WARNING : LogLevel::ERROR,
                sprintf(
                    'Video conversion failed \'%s\' with \'%s\'. "%s(%s, %s,...)"',
                    $exceptionNs[count($exceptionNs) - 1],
                    __METHOD__,
                    $e->getMessage(),
                    $inputFile,
                    $outputFile instanceof UnescapedFileInterface ? $outputFile->getFile() : $outputFile
                )
            );
            throw $e;
        }
    }
}
