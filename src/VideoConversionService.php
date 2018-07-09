<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Exception\ProcessConversionException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\ConversionParamsInterface;
use Soluble\MediaTools\Video\ConversionServiceInterface;
use Soluble\MediaTools\Video\Converter\FFMpegAdapter;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class VideoConversionService implements ConversionServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    /** @var FFMpegAdapter */
    protected $converter;

    public function __construct(FFMpegConfigInterface $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
        $this->converter    = new FFMpegAdapter($ffmpegConfig);
    }

    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want to make
     * things async...
     *
     * @see https://symfony.com/doc/current/components/process.html
     *
     * @throws FileNotFoundException when inputFile does not exists
     */
    public function getConversionProcess(string $inputFile, string $outputFile, VideoConversionParams $convertParams): Process
    {
        $this->ensureFileExists($inputFile);

        if (!$convertParams->hasParam(ConversionParamsInterface::PARAM_THREADS) && $this->ffmpegConfig->getThreads() !== null) {
            $convertParams = $convertParams->withThreads($this->ffmpegConfig->getThreads());
        }

        $arguments = $this->converter->getMappedConversionParams($convertParams);
        $ffmpegCmd = $this->converter->getCliCommand($arguments, $inputFile, $outputFile);

        $process = new Process($ffmpegCmd);
        $process->setTimeout($this->ffmpegConfig->getTimeout());
        $process->setIdleTimeout($this->ffmpegConfig->getIdleTimeout());
        $process->setEnv($this->ffmpegConfig->getEnv());

        return $process;
    }

    /**
     * Run a conversion, throw exception on error.
     *
     * @param callable|null $callback A PHP callback to run whenever there is some
     *                                tmp available on STDOUT or STDERR
     *
     * @throws FileNotFoundException      When inputFile does not exists
     * @throws ProcessConversionException When the ffmpeg process conversion failed
     */
    public function convert(string $inputFile, string $outputFile, VideoConversionParams $convertParams, ?callable $callback = null): void
    {
        $process = $this->getConversionProcess($inputFile, $outputFile, $convertParams);

        try {
            $process->mustRun($callback);
        } catch (SPException\RuntimeException $symfonyProcessException) {
            // will include: ProcessFailedException|ProcessTimedOutException|ProcessSignaledException
            throw new ProcessConversionException($process, $symfonyProcessException);
        }
    }

    /*
     * FOR LATER REFERENCE !!!
    public function convertMultiPass(string $videoFile, string $outputFile, VideoConversionParams $convertParams, VideoFilterInterface $videoFilter=null): void {

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
