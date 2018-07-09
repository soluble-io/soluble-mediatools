<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Video\Converter\FFMpegAdapter;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\ThumbServiceInterface;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class VideoThumbService implements ThumbServiceInterface
{
    use PathAssertionsTrait;

    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    /** @var FFMpegAdapter */
    protected $adapter;

    public function __construct(FFMpegConfigInterface $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
        $this->adapter      = new FFMpegAdapter($ffmpegConfig);
    }

    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want
     * handle the process your way...
     *
     * @see https://symfony.com/doc/current/components/process.html
     *
     * @throws FileNotFoundException when inputFile does not exists
     */
    public function getSymfonyProcess(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null): Process
    {
        $this->ensureFileExists($videoFile);

        $params = (new VideoConversionParams());

        if ($time !== null) {
            // For performance reasons time seek must be
            // made at the beginning of options
            $params = $params->withSeekStart($time);
        }
        $params = $params->withVideoFrames(1);

        if ($videoFilter !== null) {
            $params = $params->withVideoFilter($videoFilter);
        }

        // Quality scale for the mjpeg encoder
        $params->withVideoQualityScale(2);

        $arguments = $this->adapter->getMappedConversionParams($params);
        $ffmpegCmd = $this->adapter->getCliCommand($arguments, $videoFile, $thumbnailFile);

        $process = new Process($ffmpegCmd);
        $process->setTimeout($this->ffmpegConfig->getTimeout());
        $process->setIdleTimeout($this->ffmpegConfig->getIdleTimeout());
        $process->setEnv($this->ffmpegConfig->getEnv());

        return $process;
    }

    /**
     * @throws FileNotFoundException
     * @throws SPException\RuntimeException
     */
    public function makeThumbnail(string $videoFile, string $thumbnailFile, ?SeekTime $time = null, ?VideoFilterInterface $videoFilter = null, ?callable $callback = null): void
    {
        $process = $this->getSymfonyProcess($videoFile, $thumbnailFile, $time, $videoFilter);
        try {
            $process->mustRun($callback);
        } catch (SPException\RuntimeException $symfonyProcessException) {
            // will include: ProcessFailedException|ProcessTimedOutException|ProcessSignaledException
            throw $symfonyProcessException;
        } catch (FileNotFoundException $e) {
            throw $e;
        }
    }
}
