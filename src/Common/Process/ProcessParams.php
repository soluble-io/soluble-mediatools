<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Process;

class ProcessParams implements ProcessParamsInterface
{
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    /** @var int|null */
    protected $timeout;

    /** @var int|null */
    protected $idleTimeout;

    /** @var array<string, string|int> */
    protected $env;

    /**
     * @param int|null                  $timeout     max allowed time (in seconds) for symfony process
     * @param int|null                  $idleTimeout max allowed idle time (in seconds) for symfony process
     * @param array<string, string|int> $env         An array of additional env vars to set when running the symfony process
     */
    public function __construct(
        ?int $timeout = self::DEFAULT_TIMEOUT,
        ?int $idleTimeout = self::DEFAULT_IDLE_TIMEOUT,
        array $env = self::DEFAULT_ENV
    ) {
        $this->timeout     = $timeout;
        $this->idleTimeout = $idleTimeout;
        $this->env         = $env;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function getIdleTimeout(): ?int
    {
        return $this->idleTimeout;
    }

    public function setIdleTimeout(?int $idleTimeout): void
    {
        $this->idleTimeout = $idleTimeout;
    }

    /**
     * @return array<string, string|int>
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    /**
     * @param array<string, string|int> $env
     */
    public function setEnv(array $env): void
    {
        $this->env = $env;
    }
}
