<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterInterface;
use Soluble\MediaTools\Video\ThumbServiceInterface;

class VideoThumbService implements ThumbServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    public function __construct(FFMpegConfig $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
    }

    /**
     * @param null|VideoFilterInterface $videoFilter
     *
     * @throws FileNotFoundException
     */
    public function makeThumbnail(string $videoFile, string $outputFile, float $time = 0.0, ?VideoFilterInterface $videoFilter = null): void
    {
        $this->ensureFileExists($videoFile);

        if ($videoFilter === null) {
            $videoFilter = new EmptyVideoFilter();
        }

        $process = $this->ffmpegConfig->getProcess();

        $ffmpegCmd = $process->buildCommand(
            [
                ($time > 0.0) ? sprintf('-ss %s', $time) : '', // putting time in front is much more efficient
                sprintf('-i %s', escapeshellarg($videoFile)),  // input filename
                $videoFilter->getFFMpegCLIArgument(),                // add -vf yadif,nlmeans
                '-frames:v 1',
                '-q:v 2',
                '-y', // tell to overwrite
                sprintf('%s', escapeshellarg($outputFile)),
            ]
        );

        $process->runCommand($ffmpegCmd);
    }
}
