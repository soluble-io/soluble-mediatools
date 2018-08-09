<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Detection\InterlaceDetectGuess;
use Soluble\MediaTools\Video\Exception\AnalyzerExceptionInterface;

interface VideoAnalyzerInterface
{
    /**
     * @param int $maxFramesToAnalyze interlacement detection can be heavy, limit the number of frames to analyze
     *
     * @throws AnalyzerExceptionInterface
     */
    public function detectInterlacement(string $file, int $maxFramesToAnalyze = 1000, ?ProcessParamsInterface $processParams = null): InterlaceDetectGuess;
}
