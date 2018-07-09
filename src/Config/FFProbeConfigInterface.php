<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

interface FFProbeConfigInterface
{
    public const DEFAULT_BINARY       = 'ffmpeg';
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    public function getBinary(): string;

    public function getTimeout(): ?int;

    public function getIdleTimeout(): ?int;

    public function getEnv(): array;
}
