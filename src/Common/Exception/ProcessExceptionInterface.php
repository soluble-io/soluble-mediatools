<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Exception;

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
