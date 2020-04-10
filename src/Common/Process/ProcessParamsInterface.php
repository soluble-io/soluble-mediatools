<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Process;

interface ProcessParamsInterface
{
    /**
     * Return time-out in seconds (or null for no timeout)
     * to set when running a symfony process.
     */
    public function getTimeout(): ?float;

    /**
     * Return idle time-out in seconds (or null for no timeout)
     * to set when running a symfony process.
     */
    public function getIdleTimeout(): ?float;

    /**
     * Return additional environment variables to set
     * when running a symfony process.
     *
     * @return array<string, string|int>
     */
    public function getEnv(): array;
}
