<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Exception;

use Symfony\Component\Process\Exception as SymfonyProcessException;
use Symfony\Component\Process\Process;

class ProcessConversionException extends RuntimeException implements ProcessExceptionInterface
{
    /** @var Process */
    private $process;

    public function __construct(Process $process, SymfonyProcessException\RuntimeException $previousException)
    {
        parent::__construct(
            $previousException->getMessage(),
            $previousException->getCode(),
            $previousException
        );

        $this->process = $process;
    }

    /**
     * Return symfony process object.
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    public function wasCausedByFailure(): bool
    {
        return $this->getPrevious() instanceof SymfonyProcessException\ProcessFailedException;
    }

    public function wasCausedBySignal(): bool
    {
        return $this->getPrevious() instanceof SymfonyProcessException\ProcessSignaledException;
    }

    public function wasCausedByTimeout(): bool
    {
        return $this->getPrevious() instanceof SymfonyProcessException\ProcessTimedOutException;
    }

    /**
     * @return SymfonyProcessException\ProcessFailedException|SymfonyProcessException\ProcessSignaledException|SymfonyProcessException\ProcessTimedOutException
     */
    public function getSymfonyProcessRuntimeException(): SymfonyProcessException\RuntimeException
    {
        /**
         * @var \Symfony\Component\Process\Exception\RuntimeException
         */
        $previous = $this->getPrevious();

        return $previous;
    }
}
