<?php

declare(strict_types=1);

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
use Soluble\MediaTools\Video\Exception\InvalidParamReaderException;
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
    protected $ffmpegConfig;

    /** @var LoggerInterface|NullLogger */
    protected $logger;

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
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
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
     * @throws ProcessTimedOutException
     * @throws ProcessFailedException
     * @throws ProcessSignaledException
     * @throws InvalidParamReaderException
     * @throws RuntimeReaderException
     */
    public function convert(string $inputFile, $outputFile, VideoConvertParamsInterface $convertParams, ?callable $callback = null, ?ProcessParamsInterface $processParams = null): void
    {
        try {
            try {
                $this->ensureFileReadable($inputFile);
                $process = $this->getSymfonyProcess($inputFile, $outputFile, $convertParams, $processParams);
                $process->mustRun($callback);
            } catch (CommonException\FileNotFoundException | CommonException\FileNotReadableException $e) {
                throw new MissingInputFileException($e->getMessage());
            } catch (CommonException\UnsupportedParamValueException | CommonException\UnsupportedParamException $e) {
                throw new InvalidParamReaderException($e->getMessage());
            } catch (SPException\ProcessTimedOutException $e) {
                throw new ProcessTimedOutException($e->getProcess(), $e);
            } catch (SPException\ProcessSignaledException $e) {
                throw new ProcessSignaledException($e->getProcess(), $e);
            } catch (SPException\ProcessFailedException $e) {
                throw new ProcessFailedException($e->getProcess(), $e);
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

    /*
     * FOR LATER REFERENCE !!!
    public function convertMultiPass(string $videoFile, string $outputFile, VideoConvertParams $convertParams, VideoFilterInterface $videoFilter=null): void {

        $this->ensureFileExists($videoFile);
        if ($videoFilter === null) {
            $videoFilter = new EmptyVideoFilter();
        }


        $threads = $convertParams->getOption(VideoConversionParams::OPTION_THREADS, $this->ffmpegConfig->getThreads());

        $ffmpegBin = $this->ffmpegConfig->getBinary();

        $commonArgs = array_merge([
                $ffmpegBin,
                sprintf('-i %s', escapeshellarg($videoFile)), // input filename
                $videoFilter->getFFMpegCliArgument(), // add -vf yadif,nlmeans
                ($threads === null) ? '' : sprintf('-threads %s', $threads),
        ], $convertParams->getFFMpegArguments());

        $pass1Cmd = implode(' ', array_merge(
            $commonArgs,
            [
                '-pass 1',
                // tells VP9 to encode really fast, sacrificing quality. Useful to speed up the first pass.
                '-speed 4',
                '-y /dev/null',
            ]
        ));

        $pass2Cmd = implode( ' ', array_merge(
            $commonArgs,
            [
                '-pass 2',
                // speed 1 is a good speed vs. quality compromise.
                // Produces tmp quality typically very close to speed 0, but usually encodes much faster.
                '-speed 1',
                '-y',
                sprintf("%s", escapeshellarg($outputFile))
            ]
        ));


        $process = new Process($pass1Cmd);
        $process->setTimeout(null);
        $process->setIdleTimeout(60); // 60 seconds without tmp will stop the process
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                echo "\nRead from stdout: ".$data;
            } else { // $process::ERR === $type
                echo "\nRead from stderr: ".$data;
            }
        }

        $process = new Process($pass2Cmd);
        $process->setTimeout(null);
        $process->setIdleTimeout(60); // 60 seconds without tmp will stop the process
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                echo "\nRead from stdout: ".$data;
            } else { // $process::ERR === $type
                echo "\nRead from stderr: ".$data;
            }
        }

    }
    */
}
