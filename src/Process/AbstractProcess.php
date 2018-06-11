<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Process;

use Soluble\MediaTools\Exception\MissingBinaryException;
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

    /**
     * @throws MissingBinaryException;
     */
    public function ensureBinaryExists(): void
    {
        $binary = $this->getBinary();
        // Case of binary (no path given), we cannot tell
        if (basename($binary) === $binary) {
            $exists = true; // assume it exists
        } else {
            $exists = file_exists($binary) && is_executable($binary);
        }

        if (!$exists) {
            throw new MissingBinaryException(sprintf(
                'Missing ffprobe binary "%s"',
                $binary
            ));
        }
    }
}
