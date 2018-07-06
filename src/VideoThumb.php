<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\Filter\VideoFilterInterface;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;
use Soluble\MediaTools\Video\ProbeServiceInterface;

class VideoThumb
{
    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    /** @var ProbeServiceInterface */
    protected $videoProbe;

    public function __construct(FFMpegConfig $ffmpegConfig, ProbeServiceInterface $videoProbe)
    {
        $this->videoProbe   = $videoProbe;
        $this->ffmpegConfig = $ffmpegConfig;
        $this->ffmpegConfig->getProcess()->ensureBinaryExists();
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
    /*
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
    }*/

    public function makeThumbnails(string $videoFile, string $outputFile, float $time = 0.0, ?VideoFilterInterface $videoFilter = null): void
    {
        $this->ensureFileExists($videoFile);

        if ($videoFilter === null) {
            $videoFilter = new EmptyVideoFilter();
        }

        $process = $this->ffmpegConfig->getProcess();

        $ffmpegCmd = $process->buildCommand(
            [
                ($time > 0.0) ? sprintf('-ss %s', $time) : '', // putting time in front is much more efficient
                sprintf('-i %s', escapeshellarg($videoFile)), // input filename
                $videoFilter->getFFMpegCLIArgument(), // add -vf yadif,nlmeans
                '-frames:v 1',
                '-q:v 2',
                '-y', // tell to overwrite
                sprintf('%s', escapeshellarg($outputFile)),
            ]
        );

        $process->runCommand($ffmpegCmd);
    }

    protected function ensureFileExists(string $file): void
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exists or is not readable', $file));
        }
    }
}
