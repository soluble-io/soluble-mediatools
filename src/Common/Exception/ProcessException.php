<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Exception;

use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class ProcessException extends RuntimeException implements ProcessExceptionInterface
{
    /** @var Process */
    protected $process;

    /**
     * @param string|null $message if not set will use the previousException message
     */
    public function __construct(Process $process, SPException\RuntimeException $previousException, ?string $message = null)
    {
        if ($previousException instanceof SPException\ProcessFailedException ||
            $previousException instanceof SPException\ProcessTimedOutException ||
            $previousException instanceof SPException\ProcessSignaledException
        ) {
            $code = $previousException->getProcess()->getExitCode();
        } else {
            $code = 1;
        }

        $msg = $message ?? $previousException->getMessage();

        parent::__construct(
            $msg,
            $code ?? 1,
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

    public function getErrorOutput(): string
    {
        return $this->process->getErrorOutput();
    }

    /**
     * @return SPException\RuntimeException|SPException\ProcessFailedException|SPException\ProcessSignaledException|SPException\ProcessTimedOutException
     */
    public function getSymfonyProcessRuntimeException(): SPException\RuntimeException
    {
        /**
         * @var SPException\RuntimeException $previous
         */
        $previous = $this->getPrevious();

        return $previous;
    }
}
