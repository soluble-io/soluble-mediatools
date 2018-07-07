<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Video\Detection\InterlaceDetectGuess;
use Symfony\Component\Process\Exception\RuntimeException as SPRuntimeException;

interface DetectionServiceInterface
{
    /**
     * @param int $maxFramesToAnalyze interlacement detection can be heavy, limit the number of frames to analyze
     *
     * @throws SPRuntimeException
     * @throws FileNotFoundException
     */
    public function detectInterlacement(string $file, int $maxFramesToAnalyze = 1000): InterlaceDetectGuess;
}
