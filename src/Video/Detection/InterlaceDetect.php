<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Detection;

use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\FileNotReadableException;
use Soluble\MediaTools\Common\IO\PlatformNullFile;
use Soluble\MediaTools\Common\Process\ProcessFactory;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Exception\AnalyzerExceptionInterface;
use Soluble\MediaTools\Video\Exception\AnalyzerProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\RuntimeReaderException;
use Soluble\MediaTools\Video\Filter\IdetVideoFilter;
use Soluble\MediaTools\Video\VideoConvertParams;
use Symfony\Component\Process\Exception as SPException;

class InterlaceDetect
{
    use PathAssertionsTrait;

    public const DEFAULT_INTERLACE_MAX_FRAMES = 1000;

    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    public function __construct(FFMpegConfigInterface $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
    }

    /**
     * @throws AnalyzerExceptionInterface
     * @throws AnalyzerProcessExceptionInterface
     * @throws ProcessFailedException
     * @throws MissingInputFileException
     * @throws RuntimeReaderException
     */
    public function guessInterlacing(string $file, int $maxFramesToAnalyze = self::DEFAULT_INTERLACE_MAX_FRAMES, ?ProcessParamsInterface $processParams = null): InterlaceDetectGuess
    {
        $adapter = $this->ffmpegConfig->getAdapter();
        $params  = (new VideoConvertParams())
            ->withVideoFilter(new IdetVideoFilter()) // detect interlaced frames :)
            ->withVideoFrames($maxFramesToAnalyze)
            ->withNoAudio() // speed up the thing
            ->withOutputFormat('rawvideo')
            ->withOverwrite();

        try {
            $this->ensureFileReadable($file);

            $arguments = $adapter->getMappedConversionParams($params);
            $ffmpegCmd = $adapter->getCliCommand($arguments, $file, new PlatformNullFile());

            $pp = $processParams ?? $this->ffmpegConfig->getProcessParams();

            $process = (new ProcessFactory($ffmpegCmd, $pp))->__invoke();
            $process->mustRun();
        } catch (FileNotFoundException | FileNotReadableException $e) {
            throw new MissingInputFileException($e->getMessage());
        } catch (SPException\ProcessFailedException | SPException\ProcessTimedOutException | SPException\ProcessSignaledException $e) {
            throw new ProcessFailedException($e->getProcess(), $e);
        } catch (SPException\RuntimeException $e) {
            throw new RuntimeReaderException($e->getMessage());
        }

        $stdErr = preg_split("/(\r\n|\n|\r)/", $process->getErrorOutput());

        // Counted frames
        $interlaced_tff = 0;
        $interlaced_bff = 0;
        $progressive    = 0;
        $undetermined   = 0;
        $total_frames   = 0;

        if ($stdErr !== false) {
            foreach ($stdErr as $line) {
                if (mb_substr($line, 0, 12) !== '[Parsed_idet') {
                    continue;
                }

                $unspaced = sprintf('%s', preg_replace('/( )+/', '', $line));
                $matches  = [];
                if (preg_match_all('/TFF:(\d+)BFF:(\d+)Progressive:(\d+)Undetermined:(\d+)/i', $unspaced, $matches) < 1) {
                    continue;
                }

                //$type = strpos(strtolower($unspaced), 'single') ? 'single' : 'multi';
                $interlaced_tff += (int) $matches[1][0];
                $interlaced_bff += (int) $matches[2][0];
                $progressive += (int) $matches[3][0];
                $undetermined += (int) $matches[4][0];
                $total_frames += ((int) $matches[1][0] + (int) $matches[2][0] + (int) $matches[3][0] + (int) $matches[4][0]);
            }
        }

        return new InterlaceDetectGuess($interlaced_tff, $interlaced_bff, $progressive, $undetermined);
    }
}
