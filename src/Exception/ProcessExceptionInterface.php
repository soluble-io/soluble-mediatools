<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Exception;

use Symfony\Component\Process\Process;

interface ProcessExceptionInterface extends ExceptionInterface
{
    public function getProcess(): Process;

    public function getSymfonyProcessRuntimeException(): \Symfony\Component\Process\Exception\RuntimeException;

    public function wasCausedByFailure(): bool;

    public function wasCausedByTimeout(): bool;

    public function wasCausedBySignal(): bool;
}
