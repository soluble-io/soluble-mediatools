<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Process;

use Symfony\Component\Process\Process;

class ProcessFactory
{
    /** @var array */
    protected $command;

    /** @var null|ProcessParamsInterface */
    protected $processParams;

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
