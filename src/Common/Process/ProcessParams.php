<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Process;

class ProcessParams implements ProcessParamsInterface
{
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    /** @var float|null */
    protected $timeout;

    /** @var float|null */
    protected $idleTimeout;

    /** @var array<string, string|int> */
    protected $env;

    /**
     * @param float|null                $timeout     max allowed time (in seconds) for symfony process
     * @param float|null                $idleTimeout max allowed idle time (in seconds) for symfony process
     * @param array<string, string|int> $env         An array of additional env vars to set when running the symfony process
     */
    public function __construct(
        ?float $timeout = self::DEFAULT_TIMEOUT,
        ?float $idleTimeout = self::DEFAULT_IDLE_TIMEOUT,
        array $env = self::DEFAULT_ENV
    ) {
        $this->timeout     = $timeout;
        $this->idleTimeout = $idleTimeout;
        $this->env         = $env;
    }

    public function getTimeout(): ?float
    {
        return $this->timeout;
    }

    public function setTimeout(?float $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function getIdleTimeout(): ?float
    {
        return $this->idleTimeout;
    }

    public function setIdleTimeout(?float $idleTimeout): void
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
