<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\InfoServiceInterface;

class VideoInfoService implements InfoServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFProbeConfig */
    protected $ffprobeConfig;

    /** @var FFMpegConfig */
    protected $ffmpegConfig;

    public function __construct(FFProbeConfig $ffProbeConfig, FFMpegConfig $ffmpegConfig)
    {
        $this->ffprobeConfig = $ffProbeConfig;
        $this->ffmpegConfig  = $ffmpegConfig;
    }

    /**
     * @param string $file
     * @return VideoInfo
     * @throws FileNotFoundException
     * @throws \Throwable
     */
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
}
