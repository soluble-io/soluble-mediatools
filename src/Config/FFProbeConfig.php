<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Process\FFprobeProcess;

class FFProbeConfig
{
    /**
     * @var string
     */
    protected $binary;
    /**
     * @var FFprobeProcess
     */
    protected $process;

    public function __construct(string $binary)
    {
        $this->binary = $binary;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getProcess(): FFprobeProcess
    {
        if ($this->process === null) {
            $this->process = new FFprobeProcess($this);
        }

        return $this->process;
    }
}
