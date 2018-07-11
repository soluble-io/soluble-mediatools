<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

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

class InfoService implements InfoServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFProbeConfigInterface */
    protected $ffprobeConfig;

    public function __construct(FFProbeConfigInterface $ffProbeConfig)
    {
        $this->ffprobeConfig = $ffProbeConfig;
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

        return Info::createFromFFProbeJson($file, $output);
    }
}
