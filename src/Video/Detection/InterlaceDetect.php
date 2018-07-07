<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Detection;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Symfony\Component\Process\Exception\RuntimeException as SPRuntimeException;
use Symfony\Component\Process\Process;

class InterlaceDetect
{
    use PathAssertionsTrait;

    public const DEFAULT_INTERLACE_MAX_FRAMES = 1000;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    public function __construct(FFMpegConfig $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
    }

    /**
     * @throws SPRuntimeException
     * @throws FileNotFoundException
     */
    public function guessInterlacing(string $file, int $maxFramesToAnalyze = self::DEFAULT_INTERLACE_MAX_FRAMES): InterlaceDetectGuess
    {
        $this->ensureFileExists($file);

        $ffmpegProcess = $this->ffmpegConfig->getProcess();

        $ffmpegCmd = $ffmpegProcess->buildCommand(
            [
                sprintf('-i %s', escapeshellarg($file)),
                '-filter idet',
                sprintf('-frames:v %d', $maxFramesToAnalyze),
                '-an', // audio can be discarded
                '-f rawvideo', // output in raw
                '-y /dev/null', // discard the output
            ]
        );

        try {
            $process = new Process($ffmpegCmd);
            $process->mustRun();
        } catch (SPRuntimeException $e) {
            throw $e;
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

                $unspaced = preg_replace('/( )+/', '', $line);
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
