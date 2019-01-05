<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Common\Process;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Process\ProcessParams;

class ProcessParamsTest extends TestCase
{
    public function testEnv(): void
    {
        $env = ['param' => 'value'];

        $pp = new ProcessParams(null, null, $env);

        self::assertSame($env, $pp->getEnv());

        $newEnv = ['param' => 'value'];

        $pp->setEnv($newEnv);

        self::assertSame($newEnv, $pp->getEnv());
    }

    public function testTimeouts(): void
    {
        $pp = new ProcessParams(60.99, 10.1);

        self::assertSame(60.99, $pp->getTimeout());
        self::assertSame(10.1, $pp->getIdleTimeout());

        $pp->setTimeout(1.0);
        $pp->setIdleTimeout(1.1);

        self::assertSame(1.0, $pp->getTimeout());
        self::assertSame(1.1, $pp->getIdleTimeout());
    }
}
