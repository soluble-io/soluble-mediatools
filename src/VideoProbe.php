<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\Util\PathAssertionsTrait;
use Soluble\MediaTools\Video\Detection\InterlacementGuess;
use Symfony\Component\Process\Process;

class VideoProbe
{
    use PathAssertionsTrait;

    /** @var FFProbeConfig */
    protected $ffprobeConfig;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    /** @var mixed[] */
    protected $cache = [];

    public function __construct(FFProbeConfig $ffProbeConfig, FFMpegConfig $ffmpegConfig)
    {
        $this->ffprobeConfig = $ffProbeConfig;
        $this->ffmpegConfig  = $ffmpegConfig;
        $this->ffprobeConfig->getProcess()->ensureBinaryExists();
    }

    public function getMediaInfo(string $file): VideoInfo
    {
        $this->ensureFileExists($file);
        $process = $this->ffprobeConfig->getProcess();
        $cmd     = $process->buildCommand([
            '-v quiet',
            '-print_format json',
            '-show_format',
            '-show_streams',
            sprintf('-i "%s"', $file),
        ]);

        try {
            $jsonOutput = $process->runCommand($cmd);
        } catch (\Throwable $e) {
            //$msg = $e->getMessage();
            throw $e;
        }

        return VideoInfo::createFromFFProbeJson($file, $jsonOutput);
    }

    /**
     * @throws \Throwable
     */
    public function guessInterlacing(string $file, float $threshold = InterlacementGuess::INTERLACING_DETECTION_THRESHOLD, int $framesToAnalyze = 1000): InterlacementGuess
    {
        $cache_key = md5(sprintf('%s:%s:%s:%s', __METHOD__, $file, $threshold, $framesToAnalyze));
        if (isset($this->cache[$cache_key]) && $this->cache[$cache_key] instanceof InterlacementGuess) {
            return $this->cache[$cache_key];
        }

        $nbFrames       = $this->getMediaInfo($file)->getNbFrames();
        $analyzedFrames = ($nbFrames > 0)
                            ? min($framesToAnalyze, $nbFrames)
                            : $framesToAnalyze;

        // Step 2: Using frame detection
        $ffmpegProcess = $this->ffmpegConfig->getProcess();

        $ffmpegCmd = $ffmpegProcess->buildCommand(
            [
                sprintf('-i %s', escapeshellarg($file)),
                '-filter idet',
                sprintf('-frames:v %d', $analyzedFrames),
                '-an', // audio can be discarded
                '-f rawvideo', // output in raw
                '-y /dev/null', // discard the output
            ]
        );

        $process = new Process($ffmpegCmd);
        $process->mustRun();
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

        $guess                   = new InterlacementGuess($interlaced_tff, $interlaced_bff, $progressive, $undetermined);
        $this->cache[$cache_key] = $guess;

        return $guess;
    }

    /*
     * Use ffprobe to determine if a video is POTENTIALLY interlaced...
     * Warning:
     *  - There's no absolute truth in that. but if false it's not
     *  - Very slow.
     */
    /*
    public function isVideoPotentiallyInterlaced(string $file): bool {

        $this->ensureFileExists($file);

        // Step 1: ffprobe to see if interlaced
        $cmd = $this->buildFFProbeCommand([
            sprintf('-i "%s"', $file),
            '-v quiet',             // do not display any messages
            '-select_streams v',    // select video stream
            '-show_entries "frame=pkt_pts_time,pkt_duration_time,interlaced_frame"',
            '-print_format json'
        ]);

        $output = $this->runCommand($cmd);

        $decoded = json_decode($output, true);
        if ($decoded === null || $output === '') {
            throw new JsonParseException(sprintf(
                'Cannot parse output from ffprobe (%s)', $cmd
            ));
        }
        $frame_stats = [
            'nb_frames' => count($decoded['frames']),
            'nb_interlaced' => 0,
        ];
        foreach ($decoded['frames'] as $frame) {
            if ((int) $frame['interlaced_frame'] === 1) {
                $frame_stats['nb_interlaced']++;
            }
        }


        if ($frame_stats['nb_interlaced'] === 0 && $frame_stats['nb_frames'] > 0) {
            // here we know for sure it is not
            return false;
        }

        return true;
    }
    */
}
