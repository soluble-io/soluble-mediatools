<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Exception\ProcessConversionException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\Converter\ParamsInterface;
use Symfony\Component\Process\Exception as ProcessException;
use Symfony\Component\Process\Process;

class ConverterService implements ConverterServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    /** @var ProbeService */
    protected $videoProbe;

    public function __construct(FFMpegConfig $ffmpegConfig, ProbeService $videoProbe)
    {
        $this->videoProbe   = $videoProbe;
        $this->ffmpegConfig = $ffmpegConfig;
        $this->ffmpegConfig->getProcess()->ensureBinaryExists();
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
    public function getConversionProcess(string $inputFile, string $outputFile, ConverterParams $convertParams): Process
    {
        $this->ensureFileExists($inputFile);

        $process = $this->ffmpegConfig->getProcess();

        if (!$convertParams->hasOption(ParamsInterface::PARAM_THREADS) && $this->ffmpegConfig->getThreads() !== null) {
            $convertParams = $convertParams->withThreads($this->ffmpegConfig->getThreads());
        }

        $ffmpegCmd = $process->buildCommand(
            array_merge(
                [
                    sprintf('-i %s', escapeshellarg($inputFile)), // input filename
                ],
                $convertParams->getFFMpegArguments(),
                [
                    '-y', // tell to overwrite
                    sprintf('%s', escapeshellarg($outputFile)),
                ]
            )
        );

        $process = new Process($ffmpegCmd);
        $process->setTimeout($this->ffmpegConfig->getConversionTimeout());
        $process->setIdleTimeout($this->ffmpegConfig->getConversionIdleTimeout());

        return $process;
    }

    /**
     * Run a conversion, throw exception on error.
     *
     * @param callable|null                 $callback A PHP callback to run whenever there is some
     *                                                output available on STDOUT or STDERR
     * @param array<string,string|int>|null $env      An array of env vars to set
     *                                                when running the process
     *
     * @throws FileNotFoundException      When inputFile does not exists
     * @throws ProcessConversionException When the ffmpeg process conversion failed
     */
    public function convert(string $inputFile, string $outputFile, ConverterParams $convertParams, ?callable $callback = null, ?array $env = null): void
    {
        $process = $this->getConversionProcess($inputFile, $outputFile, $convertParams);

        try {
            $process->mustRun($callback, (is_array($env) ? $env : $this->ffmpegConfig->getConversionEnv()));
        } catch (ProcessException\RuntimeException $symfonyProcessException) {
            // will include: ProcessFailedException|ProcessTimedOutException|ProcessSignaledException
            throw new ProcessConversionException($process, $symfonyProcessException);
        } catch (FileNotFoundException $e) {
            throw $e;
        }
    }

    /*
     * FOR LATER REFERENCE !!!
    public function convertMultiPass(string $videoFile, string $outputFile, ConverterParams $convertParams, VideoFilterInterface $videoFilter=null): void {

        $this->ensureFileExists($videoFile);
        if ($videoFilter === null) {
            $videoFilter = new EmptyVideoFilter();
        }


        $threads = $convertParams->getOption(ConverterParams::OPTION_THREADS, $this->ffmpegConfig->getThreads());

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
                // Produces output quality typically very close to speed 0, but usually encodes much faster.
                '-speed 1',
                '-y',
                sprintf("%s", escapeshellarg($outputFile))
            ]
        ));


        $process = new Process($pass1Cmd);
        $process->setTimeout(null);
        $process->setIdleTimeout(60); // 60 seconds without output will stop the process
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
        $process->setIdleTimeout(60); // 60 seconds without output will stop the process
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
