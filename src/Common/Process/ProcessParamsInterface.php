<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Process;

interface ProcessParamsInterface
{
    /**
     * Return time-out in seconds (or null for no timeout)
     * to set when running a symfony process.
     */
    public function getTimeout(): ?int;

    /**
     * Return idle time-out in seconds (or null for no timeout)
     * to set when running a symfony process.
     */
    public function getIdleTimeout(): ?int;

    /**
     * Return additional environment variables to set
     * when running a symfony process.
     *
     * @return array<string, string|int>
     */
    public function getEnv(): array;
}
