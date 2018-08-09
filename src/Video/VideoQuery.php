<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Process\ProcessFactory;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Video\Exception\InfoExceptionInterface;
use Soluble\MediaTools\Video\Exception\InfoProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\RuntimeException;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class VideoQuery implements VideoQueryInterface
{
    use PathAssertionsTrait;

    /** @var FFProbeConfigInterface */
    protected $ffprobeConfig;

    /** @var LoggerInterface|NullLogger */
    protected $logger;

    public function __construct(FFProbeConfigInterface $ffProbeConfig, ?LoggerInterface $logger = null)
    {
        $this->ffprobeConfig = $ffProbeConfig;
        $this->logger        = $logger ?? new NullLogger();
    }

    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want to make
     * things your way...
     *
     * @see https://symfony.com/doc/current/components/process.html
     */
    public function getSymfonyProcess(string $inputFile, ?ProcessParamsInterface $processParams = null): Process
    {
        $ffprobeCmd = trim(sprintf(
            '%s %s %s',
            $this->ffprobeConfig->getBinary(),
            implode(' ', [
                '-v quiet',
                '-print_format json',
                '-show_format',
                '-show_streams',
            ]),
            sprintf('-i %s', escapeshellarg($inputFile))
        ));

        $pp = $processParams ?? $this->ffprobeConfig->getProcessParams();

        return (new ProcessFactory($ffprobeCmd, $pp))();
    }

    /**
     * @throws InfoExceptionInterface
     * @throws InfoProcessExceptionInterface
     * @throws ProcessFailedException
     * @throws MissingInputFileException
     * @throws RuntimeException
     */
    public function getInfo(string $file): Info
    {
        try {
            try {
                $this->ensureFileExists($file);
                $process = $this->getSymfonyProcess($file);

                $process->mustRun();
                $output = $process->getOutput();
            } catch (FileNotFoundException $e) {
                throw new MissingInputFileException($e->getMessage());
            } catch (SPException\ProcessFailedException | SPException\ProcessTimedOutException | SPException\ProcessSignaledException $e) {
                throw new ProcessFailedException($e->getProcess(), $e);
            } catch (SPException\RuntimeException $e) {
                throw new RuntimeException($e->getMessage());
            }
        } catch (\Throwable $e) {
            $exceptionNs = explode('\\', get_class($e));
            $this->logger->log(
                ($e instanceof MissingInputFileException) ? LogLevel::WARNING : LogLevel::ERROR,
                sprintf(
                    'Video info retrieval failed \'%s\' with \'%s\'. "%s(%s)"',
                    $exceptionNs[count($exceptionNs) - 1],
                    __METHOD__,
                    $e->getMessage(),
                    $file
                )
            );
            throw $e;
        }

        return Info::createFromFFProbeJson($file, $output);
    }
}
