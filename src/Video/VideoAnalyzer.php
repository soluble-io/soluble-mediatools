<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Detection\InterlaceDetect;
use Soluble\MediaTools\Video\Detection\InterlaceDetectGuess;
use Soluble\MediaTools\Video\Exception\AnalyzerExceptionInterface;
use Soluble\MediaTools\Video\Exception\AnalyzerProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\RuntimeReaderException;

class VideoAnalyzer implements VideoAnalyzerInterface
{
    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(FFMpegConfigInterface $ffmpegConfig, ?LoggerInterface $logger = null)
    {
        $this->ffmpegConfig = $ffmpegConfig;
        $this->logger       = $logger ?? new NullLogger();
    }

    /**
     * @param int $maxFramesToAnalyze interlacement detection can be heavy, limit the number of frames to analyze
     *
     * @throws AnalyzerExceptionInterface
     * @throws AnalyzerProcessExceptionInterface
     * @throws ProcessFailedException
     * @throws MissingInputFileException
     * @throws RuntimeReaderException
     */
    public function detectInterlacement(string $file, int $maxFramesToAnalyze = InterlaceDetect::DEFAULT_INTERLACE_MAX_FRAMES, ?ProcessParamsInterface $processParams = null): InterlaceDetectGuess
    {
        $interlaceDetect = new InterlaceDetect($this->ffmpegConfig);

        return $interlaceDetect->guessInterlacing($file, $maxFramesToAnalyze, $processParams);
    }
}
