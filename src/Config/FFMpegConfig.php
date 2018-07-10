<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

class FFMpegConfig implements FFMpegConfigInterface
{
    public const DEFAULT_BINARY       = 'ffmpeg';
    public const DEFAULT_THREADS      = null;
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    /** @var string */
    protected $binary;

    /** @var int|null */
    protected $threads;

    /** @var float|null */
    protected $timeout;

    /** @var float|null */
    protected $idleTimeout;

    /** @var array<string, string|int> */
    protected $env;

    /**
     * @param string                    $ffmpegBinary
     * @param int|null                  $threads      number fo threads used for conversion, null means single threads, 0 all cores, ....
     * @param float|null                $timeout      max allowed time (in seconds) for conversion, null for no timeout
     * @param float|null                $idleTimeout  max allowed idle time (in seconds) for conversion, null for no timeout
     * @param array<string, string|int> $env          An array of additional env vars to set when running the ffmpeg conversion process
     */
    public function __construct(
        string $ffmpegBinary = self::DEFAULT_BINARY,
        ?int $threads = self::DEFAULT_THREADS,
        ?float $timeout = self::DEFAULT_TIMEOUT,
        ?float $idleTimeout = self::DEFAULT_IDLE_TIMEOUT,
        array $env = self::DEFAULT_ENV
    ) {
        $this->binary      = $ffmpegBinary;
        $this->threads     = $threads;
        $this->timeout     = $timeout;
        $this->idleTimeout = $idleTimeout;
        $this->env         = $env;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getThreads(): ?int
    {
        return $this->threads;
    }

    public function getTimeout(): ?float
    {
        return $this->timeout;
    }

    public function getIdleTimeout(): ?float
    {
        return $this->idleTimeout;
    }

    /**
     * @return array<string, string|int>
     */
    public function getEnv(): array
    {
        return $this->env;
    }
}
