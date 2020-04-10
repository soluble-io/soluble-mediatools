<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Common\Math;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Math\NumberConversion;

class NumberConversionTest extends TestCase
{
    public function test(): void
    {
        self::assertEquals(1.2, NumberConversion::truncateFloat(1.22333, 1));
        self::assertEquals(1.2, NumberConversion::truncateFloat(1.29333, 1));

        self::assertEquals(1.22, NumberConversion::truncateFloat(1.229333, 2));

        self::assertEquals(1, NumberConversion::truncateFloat(1.229333, 0));

        self::assertEquals(-1, NumberConversion::truncateFloat(-1.129333, 0));
        self::assertEquals(-1.1, NumberConversion::truncateFloat(-1.199333, 1));
        self::assertEquals(-1.1, NumberConversion::truncateFloat(-1.119333, 1));
    }
}
