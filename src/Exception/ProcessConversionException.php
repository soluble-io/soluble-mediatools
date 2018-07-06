<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Exception;

use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class ProcessConversionException extends RuntimeException implements ProcessExceptionInterface
{
    public const FAILURE_TYPE_PROCESS = 'PROCESS';
    public const FAILURE_TYPE_TIMEOUT = 'TIMEOUT';
    public const FAILURE_TYPE_SIGNAL  = 'SIGNAL';
    public const FAILURE_TYPE_RUNTIME = 'RUNTIME';



    /** @var Process */
    private $process;

    /**
     * @var string
     */
    private $failureType;

    public function __construct(Process $process, SPException\RuntimeException $previousException)
    {
        if ($previousException instanceof SPException\ProcessFailedException) {
            $code = $previousException->getProcess()->getExitCode();
            $type = self::FAILURE_TYPE_PROCESS;
        } elseif ($previousException instanceof SPException\ProcessTimedOutException) {
            $code = $previousException->getProcess()->getExitCode();
            $type = self::FAILURE_TYPE_SIGNAL;
        } elseif ($previousException instanceof SPException\ProcessSignaledException) {
            $type = self::FAILURE_TYPE_TIMEOUT;
            $code = $previousException->getProcess()->getExitCode();
        } else {
            $code = 1;
            $type = self::FAILURE_TYPE_RUNTIME;
        }

        parent::__construct(
            $previousException->getMessage(),
            $code,
            $previousException
        );

        $this->process = $process;
        $this->failureType = $type;
    }

    /**
     * Return symfony process object.
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    public function wasCausedByProcess(): bool
    {
        return $this->failureType === self::FAILURE_TYPE_PROCESS;
    }

    public function wasCausedBySignal(): bool
    {
        return $this->failureType === self::FAILURE_TYPE_SIGNAL;
    }

    public function wasCausedByTimeout(): bool
    {
        return $this->failureType === self::FAILURE_TYPE_TIMEOUT;
    }

    /**
     * Whether the failure is due to 'PROCESS', 'TIMEOUT', 'SIGNAL' or (generic) 'RUNTIME' exception
     */
    public function getFailureType(): string
    {
        return $this->failureType;
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
