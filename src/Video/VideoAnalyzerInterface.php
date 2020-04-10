<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

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
