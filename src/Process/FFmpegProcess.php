<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Process;

use Soluble\MediaTools\Config\FFMpegConfig;

class FFmpegProcess extends AbstractProcess
{
    /** @var FFMpegConfig */
    protected $config;

    public function __construct(FFMpegConfig $config)
    {
        $this->config = $config;
    }

    public function getBinary(): string
    {
        return $this->config->getBinary();
    }

    /**
     * @param array<int|string, string> $arguments
     */
    public function buildCommand(array $arguments): string
    {
        return sprintf(
            '%s %s',
            $this->config->getBinary(),
            implode(' ', $arguments)
        );
    }
}
