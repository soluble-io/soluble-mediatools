<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\InfoServiceInterface;
use Symfony\Component\Process\Process;

class VideoInfoService implements InfoServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFProbeConfig */
    protected $ffprobeConfig;

    public function __construct(FFProbeConfig $ffProbeConfig)
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
        $this->ensureFileExists($inputFile);

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
     * @param string $file
     *
     * @return VideoInfo
     *
     * @throws FileNotFoundException
     * @throws \Throwable
     */
    public function getMediaInfo(string $file): VideoInfo
    {
        $process = $this->getFFProbeProcess($file);

        try {
            $process->mustRun();
            $output = $process->getOutput();
        } catch (\Throwable $e) {
            throw $e;
        }

        return VideoInfo::createFromFFProbeJson($file, $output);
    }
}
