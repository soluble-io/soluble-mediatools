<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

class FFProbeConfig implements FFProbeConfigInterface
{
    public const DEFAULT_BINARY       = 'ffmpeg';
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    /** @var string */
    protected $binary;

    /** @var float|null */
    protected $timeout;

    /** @var float|null */
    protected $idleTimeout;

    /** @var array<string, string|int> */
    protected $env;

    /**
     * @param string                    $ffprobeBinary FFProbeBinary, by default ffprobe
     * @param float|null                $timeout       max allowed time (in seconds) for conversion, null for no timeout
     * @param float|null                $idleTimeout   max allowed idle time (in seconds) for conversion, null for no timeout
     * @param array<string, string|int> $env           An array of additional env vars to set when running the ffprobe process
     */
    public function __construct(
        string $ffprobeBinary = self::DEFAULT_BINARY,
        ?float $timeout = self::DEFAULT_TIMEOUT,
        ?float $idleTimeout = self::DEFAULT_IDLE_TIMEOUT,
        array $env = self::DEFAULT_ENV
    ) {
        $this->binary      = $ffprobeBinary;
        $this->timeout     = $timeout;
        $this->idleTimeout = $idleTimeout;
        $this->env         = $env;
    }

    public function getBinary(): string
    {
        return $this->binary;
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
