<?php

declare(strict_types=1);

namespace MediaToolsTest\Common\Exception;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\ProcessException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ProcessExceptionTest extends TestCase
{
    public function testUsage(): void
    {
        $process = new Process(['ls -la']);
        $se      = new ProcessTimedOutException($process, ProcessTimedOutException::TYPE_GENERAL);
        $e       = new ProcessException($process, $se);

        self::assertSame($process, $e->getProcess());
        self::assertSame($se, $e->getSymfonyProcessRuntimeException());

        self::expectException(LogicException::class);

        $e->getErrorOutput();
    }
}
