<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Process\FFmpegProcess;

class FFMpegConfig
{
    /** @var string */
    protected $binary;

    /** @var FFmpegProcess */
    protected $process;

    /** @var int|null */
    protected $threads;

    /** @var int|null */
    protected $conversionTimeout;

    /** @var int|null */
    protected $conversionIdleTimeout;

    /**
     * FFMpegConfig constructor.
     *
     * @param string   $binary
     * @param int|null $threads               number fo threads used for conversion, null means single threads, 0 all cores, ....
     * @param int|null $conversionTimeout     max allowed time (in seconds) for conversion
     * @param int|null $conversionIdleTimeout max allowed idle time (in seconds) for conversion
     */
    public function __construct(string $binary, ?int $threads = null, ?int $conversionTimeout = null, ?int $conversionIdleTimeout = null)
    {
        $this->binary                = $binary;
        $this->threads               = $threads;
        $this->conversionTimeout     = $conversionTimeout;
        $this->conversionIdleTimeout = $conversionIdleTimeout;
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

    public function getConversionIdleTimeout(): ?int
    {
        return $this->conversionIdleTimeout;
    }

    public function getProcess(): FFmpegProcess
    {
        if ($this->process === null) {
            $this->process = new FFmpegProcess($this);
        }

        return $this->process;
    }
}
