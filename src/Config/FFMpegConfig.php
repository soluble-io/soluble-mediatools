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

    public function __construct(string $binary, ?int $threads = null)
    {
        $this->binary  = $binary;
        $this->threads = $threads;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getThreads(): ?int
    {
        return $this->threads;
    }

    public function getProcess(): FFmpegProcess
    {
        if ($this->process === null) {
            $this->process = new FFmpegProcess($this);
        }

        return $this->process;
    }
}
