<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Process;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class AbstractProcess
{
    abstract public function buildCommand(array $arguments): string;

    abstract public function getBinary(): string;

    public function runCommand(string $cmd): string
    {
        $process = new Process($cmd);
        try {
            $process->mustRun();

            return $process->getOutput();
        } catch (ProcessFailedException $e) {
            throw $e;
        }
    }
}
