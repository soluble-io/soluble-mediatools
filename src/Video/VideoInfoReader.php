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
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Soluble\MediaTools\Common\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Common\Cache\NullCache;
use Soluble\MediaTools\Common\Exception\FileEmptyException;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\FileNotReadableException;
use Soluble\MediaTools\Common\Exception\JsonParseException;
use Soluble\MediaTools\Common\Process\ProcessFactory;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Video\Exception\InfoProcessReaderExceptionInterface;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\Exception\InvalidFFProbeJsonException;
use Soluble\MediaTools\Video\Exception\MissingFFProbeBinaryException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\RuntimeReaderException;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

final class VideoInfoReader implements VideoInfoReaderInterface
{
    use PathAssertionsTrait;

    /** @var FFProbeConfigInterface */
    private $ffprobeConfig;

    /** @var LoggerInterface */
    private $logger;

    /** @var CacheInterface */
    private $cache;

    public function __construct(FFProbeConfigInterface $ffProbeConfig, ?LoggerInterface $logger = null, ?CacheInterface $cache = null)
    {
        $this->ffprobeConfig = $ffProbeConfig;
        $this->logger        = $logger ?? new NullLogger();
        $this->cache         = $cache ?? new NullCache();
    }

    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want to make
     * things your way...
     *
     * @see https://symfony.com/doc/current/components/process.html
     */
    public function getSymfonyProcess(string $inputFile, ?ProcessParamsInterface $processParams = null): Process
    {
        $ffprobeCmd = [
            $this->ffprobeConfig->getBinary(),
            '-v',
            'warning',
            '-print_format',
            'json',
            '-show_format',
            '-show_streams',
            '-i',
            $inputFile,
        ];

        $pp = $processParams ?? $this->ffprobeConfig->getProcessParams();

        return (new ProcessFactory($ffprobeCmd, $pp))();
    }

    /**
     * @throws InfoReaderExceptionInterface
     * @throws InfoProcessReaderExceptionInterface
     * @throws ProcessFailedException
     * @throws InvalidFFProbeJsonException
     * @throws MissingInputFileException
     * @throws MissingFFProbeBinaryException
     * @throws RuntimeReaderException
     */
    public function getInfo(string $file, ?CacheInterface $cache = null): VideoInfo
    {
        $cache = $cache ?? $this->cache;

        // global try/catch to call logger

        try {
            try {
                $this->ensureFileReadable($file, true);
            } catch (FileNotFoundException | FileNotReadableException | FileEmptyException $e) {
                throw new MissingInputFileException($e->getMessage());
            }

            $process = $this->getSymfonyProcess($file);

            try {
                $key       = $this->getCacheKey($process, $file);
                $videoInfo = $this->loadVideoInfoFromCacheKey($file, $cache, $key);
                if ($videoInfo === null) {
                    // cache failure or corrupted, let's fallback to running ffprobe
                    $cache->set($key, null);
                    $process->mustRun();
                    $output    = $process->getOutput();
                    $videoInfo = VideoInfo::createFromFFProbeJson($file, $output, $this->logger);
                    $cache->set($key, $output);
                }

                // Exception mapping
            } catch (JsonParseException $e) {
                throw new InvalidFFProbeJsonException($e->getMessage());
            } catch (SPException\ProcessFailedException $e) {
                $process = $e->getProcess();
                if ($process->getExitCode() === 127 ||
                    mb_strpos(mb_strtolower($process->getExitCodeText()), 'command not found') !== false) {
                    throw new MissingFFProbeBinaryException($process, $e);
                }
                throw new ProcessFailedException($process, $e);
            } catch (SPException\ProcessTimedOutException | SPException\ProcessSignaledException $e) {
                throw new ProcessFailedException($e->getProcess(), $e);
            } catch (SPException\RuntimeException $e) {
                throw new RuntimeReaderException($e->getMessage());
            }
        } catch (\Throwable $e) {
            $this->logException($e, $file);
            throw $e;
        }

        return $videoInfo;
    }

    private function loadVideoInfoFromCacheKey(string $file, CacheInterface $cache, string $cacheKey): ?VideoInfo
    {
        $output = $cache->get($cacheKey, null);
        if ($output !== null && $output !== '') {
            try {
                return VideoInfo::createFromFFProbeJson($file, $output, $this->logger);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    private function logException(\Throwable $e, string $file): void
    {
        $exceptionNs = explode('\\', get_class($e));
        $this->logger->log(
            ($e instanceof MissingInputFileException) ? LogLevel::WARNING : LogLevel::ERROR,
            sprintf(
                'VideoInfoReader %s: \'%s\'. (%s)',
                $exceptionNs[count($exceptionNs) - 1],
                $file,
                $e->getMessage()
            )
        );
    }

    private function getCacheKey(Process $process, string $file): string
    {
        return sha1(sprintf(
            '%s | %s | %s | %s | %s',
            __METHOD__,
            $process->getCommandLine(),
            $file,
            (string) filesize($file),
            (string) filemtime($file)
        ));
    }
}
