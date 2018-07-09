<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Detection\InterlaceDetect;
use Soluble\MediaTools\Video\Detection\InterlaceDetectGuess;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Symfony\Component\Process\Exception\RuntimeException as SPRuntimeException;

class VideoDetectionService implements DetectionServiceInterface
{
    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    public function __construct(FFMpegConfigInterface $ffmpegConfig)
    {
        $this->ffmpegConfig  = $ffmpegConfig;
    }

    /**
     * @param int $maxFramesToAnalyze interlacement detection can be heavy, limit the number of frames to analyze
     *
     * @throws SPRuntimeException
     * @throws FileNotFoundException
     */
    public function detectInterlacement(string $file, int $maxFramesToAnalyze = InterlaceDetect::DEFAULT_INTERLACE_MAX_FRAMES): InterlaceDetectGuess
    {
        $interlaceDetect = new InterlaceDetect($this->ffmpegConfig);

        return $interlaceDetect->guessInterlacing($file, $maxFramesToAnalyze);
    }
}
