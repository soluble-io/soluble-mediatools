<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Filter\Video\EmptyVideoFilter;
use Soluble\MediaTools\Filter\Video\VideoFilterChain;
use Soluble\MediaTools\Filter\Video\VideoFilterInterface;
use Soluble\MediaTools\Filter\Video\VideoFilterTypeDenoiseInterface;
use Soluble\MediaTools\Filter\Video\YadifVideoFilter;
use Symfony\Component\Process\Process;

class VideoTranscode
{
    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    /** @var VideoProbe */
    protected $videoProbe;

    public function __construct(FFMpegConfig $ffmpegConfig, VideoProbe $videoProbe)
    {
        $this->videoProbe   = $videoProbe;
        $this->ffmpegConfig = $ffmpegConfig;
        $this->ffmpegConfig->getProcess()->ensureBinaryExists();
    }

    /*
    public function transcodeMultiPass(string $videoFile, string $outputFile, VideoTranscodeParams $transcodeParams, VideoFilterInterface $videoFilter=null): void {

        $this->ensureFileExists($videoFile);
        if ($videoFilter === null) {
            $videoFilter = new EmptyVideoFilter();
        }


        $threads = $transcodeParams->getOption(VideoTranscodeParams::OPTION_THREADS, $this->ffmpegConfig->getThreads());

        $ffmpegBin = $this->ffmpegConfig->getBinary();

        $commonArgs = array_merge([
                $ffmpegBin,
                sprintf('-i %s', escapeshellarg($videoFile)), // input filename
                $videoFilter->getFFMpegCliArgument(), // add -vf yadif,nlmeans
                ($threads === null) ? '' : sprintf('-threads %s', $threads),
        ], $transcodeParams->getFFMpegArguments());

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

    public function transcode(string $videoFile, string $outputFile, VideoTranscodeParams $transcodeParams, ?VideoFilterInterface $videoFilter = null): void
    {
        $this->ensureFileExists($videoFile);

        if ($videoFilter === null) {
            $videoFilter = new EmptyVideoFilter();
        }

        $process = $this->ffmpegConfig->getProcess();

        $threads = $transcodeParams->getOption(VideoTranscodeParams::OPTION_THREADS, $this->ffmpegConfig->getThreads());

        $ffmpegCmd = $process->buildCommand(
            array_merge(
                [
                    sprintf('-i %s', escapeshellarg($videoFile)), // input filename
                    $videoFilter->getFFMpegCLIArgument(), // add -vf yadif,nlmeans
                ],
                $transcodeParams->getFFMpegArguments(),
                [
                    ($threads !== null) ? sprintf('-threads %s', $threads) : '',
                    '-y', // tell to overwrite
                    sprintf('%s', escapeshellarg($outputFile)),
                ]
            )
        );

        echo $ffmpegCmd;

        $process = new Process($ffmpegCmd);
        $process->setTimeout(null);
        // 60 seconds without output will stop the process
        $process->setIdleTimeout(60);
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                echo "\nRead from stdout: " . $data;
            } else { // $process::ERR === $type
                echo "\nRead from stderr: " . $data;
            }
        }
    }

    /**
     * Try to guess if the original video is interlaced (bff, tff) and
     * return ffmpeg yadif filter argument and add denoise filter if any.
     *
     * @see https://ffmpeg.org/ffmpeg-filters.html (section yadif)
     * @see https://askubuntu.com/a/867203
     *
     * @return VideoFilterInterface|VideoFilterChain|EmptyVideoFilter|YadifVideoFilter
     */
    public function getDeintFilter(string $videoFile, ?VideoFilterTypeDenoiseInterface $denoiseFilter = null): VideoFilterInterface
    {
        $guess       = $this->videoProbe->guessInterlacing($videoFile);
        $deintFilter = $guess->getDeinterlaceVideoFilter();
        // skip all filters if video is not interlaces
        if ($deintFilter instanceof EmptyVideoFilter) {
            return $deintFilter;
        }
        if ($denoiseFilter !== null) {
            $videoFilterChain = new VideoFilterChain();
            $videoFilterChain->addFilter($deintFilter);
            $videoFilterChain->addFilter($denoiseFilter);

            return $videoFilterChain;
        }

        return $deintFilter;
    }

    protected function ensureFileExists(string $file): void
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exists or is not readable', $file));
        }
    }
}
