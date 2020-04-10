<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Process;

use Symfony\Component\Process\Process;

final class ProcessFactory
{
    /** @var array */
    private $command;

    /** @var null|ProcessParamsInterface */
    private $processParams;

    /**
     * @param array                       $command
     * @param null|ProcessParamsInterface $processParams
     */
    public function __construct(array $command, ?ProcessParamsInterface $processParams = null)
    {
        $this->command       = $command;
        $this->processParams = $processParams;
    }

    public function __invoke(): Process
    {
        $process = new Process($this->command);
        if ($this->processParams !== null) {
            $process->setTimeout($this->processParams->getTimeout());
            $process->setIdleTimeout($this->processParams->getIdleTimeout());
            $process->setEnv($this->processParams->getEnv());
        }

        return $process;
    }
}
