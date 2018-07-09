<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Exception;

use Symfony\Component\Process\Exception as SymfonyProcessException;
use Symfony\Component\Process\Process;

interface ProcessExceptionInterface extends ExceptionInterface
{
    public function getProcess(): Process;

    /**
     * @return SymfonyProcessException\ProcessFailedException|SymfonyProcessException\ProcessSignaledException|SymfonyProcessException\ProcessTimedOutException
     */
    public function getSymfonyProcessRuntimeException(): SymfonyProcessException\RuntimeException;
}
