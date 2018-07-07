<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

class FFMpegConfig
{
    /** @var string */
    protected $binary;

    /** @var int|null */
    protected $threads;

    /** @var int|null */
    protected $conversionTimeout;

    /** @var int|null */
    protected $conversionIdleTimeout;

    /** @var array<string, string|int> */
    protected $conversionEnv;

    /**
     * FFMpegConfig constructor.
     *
     * @param string                    $binary
     * @param int|null                  $threads               number fo threads used for conversion, null means single threads, 0 all cores, ....
     * @param int|null                  $conversionTimeout     max allowed time (in seconds) for conversion
     * @param int|null                  $conversionIdleTimeout max allowed idle time (in seconds) for conversion
     * @param array<string, string|int> $conversionEnv         An array of additional env vars to set when running the ffmpeg conversion process
     */
    public function __construct(string $binary, ?int $threads = null, ?int $conversionTimeout = null, ?int $conversionIdleTimeout = null, array $conversionEnv = [])
    {
        $this->binary                = $binary;
        $this->threads               = $threads;
        $this->conversionTimeout     = $conversionTimeout;
        $this->conversionIdleTimeout = $conversionIdleTimeout;
        $this->conversionEnv         = $conversionEnv;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getThreads(): ?int
    {
        return $this->threads;
    }

    public function getConversionTimeout(): ?int
    {
        return $this->conversionTimeout;
    }

    /**
     * @return array<string, string|int>
     */
    public function getConversionEnv(): array
    {
        return $this->conversionEnv;
    }

    public function getConversionIdleTimeout(): ?int
    {
        return $this->conversionIdleTimeout;
    }
}
