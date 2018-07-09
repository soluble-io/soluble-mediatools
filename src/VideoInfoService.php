<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\Exception\InfoExceptionInterface;
use Soluble\MediaTools\Video\Exception\InfoProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\RuntimeException;
use Soluble\MediaTools\Video\Info;
use Soluble\MediaTools\Video\InfoServiceInterface;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class VideoInfoService implements InfoServiceInterface
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
     *
     * @throws FileNotFoundException when inputFile does not exists
     */
    public function getFFProbeProcess(string $inputFile): Process
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

        $process = new Process($ffprobeCmd);
        $process->setTimeout($this->ffprobeConfig->getTimeout());
        $process->setIdleTimeout($this->ffprobeConfig->getIdleTimeout());
        $process->setEnv($this->ffprobeConfig->getEnv());

        return $process;
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
            $process = $this->getFFProbeProcess($file);

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
