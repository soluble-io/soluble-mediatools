<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Exception;

use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class ProcessException extends RuntimeException implements ProcessExceptionInterface
{
    /** @var Process */
    protected $process;

    public function __construct(Process $process, SPException\RuntimeException $previousException)
    {
        if ($previousException instanceof SPException\ProcessFailedException ||
            $previousException instanceof SPException\ProcessTimedOutException ||
            $previousException instanceof SPException\ProcessSignaledException
        ) {
            $code = $previousException->getProcess()->getExitCode();
        } else {
            $code = 1;
        }

        parent::__construct(
            $previousException->getMessage(),
            $code,
            $previousException
        );

        $this->process     = $process;
    }

    /**
     * Return symfony process object.
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    public function geErrorOutput(): string
    {
        return $this->process->getErrorOutput();
    }

    /**
     * @return SPException\RuntimeException|SPException\ProcessFailedException|SPException\ProcessSignaledException|SPException\ProcessTimedOutException
     */
    public function getSymfonyProcessRuntimeException(): SPException\RuntimeException
    {
        /**
         * @var SPException\RuntimeException
         */
        $previous = $this->getPrevious();

        return $previous;
    }
}
