<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Detection\InterlaceDetect;
use Soluble\MediaTools\Video\Detection\InterlaceGuess;
use Symfony\Component\Process\Exception\RuntimeException as SPRuntimeException;

class DetectionService implements DetectionServiceInterface
{
    /** @var FFProbeConfig */
    protected $ffprobeConfig;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    /** @var array */
    protected $interlaceDetectCache;

    public function __construct(FFProbeConfig $ffProbeConfig, FFMpegConfig $ffmpegConfig)
    {
        $this->ffprobeConfig = $ffProbeConfig;
        $this->ffmpegConfig  = $ffmpegConfig;
    }

    public function getInterlaceDetect(): InterlaceDetect
    {
    }

    /**
     * @param int $maxFramesToAnalyze interlacement detection can be heavy, limit the number of frames to analyze
     *
     * @throws SPRuntimeException
     * @throws FileNotFoundException
     */
    public function detectInterlacement(string $file, int $maxFramesToAnalyze = 1000): InterlaceGuess
    {
        $interlaceDetect = new InterlaceDetect($this->ffmpegConfig);

        return $interlaceDetect->guessInterlacing($file, $maxFramesToAnalyze);
    }
}
