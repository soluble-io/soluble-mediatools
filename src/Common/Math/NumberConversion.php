<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Math;

final class NumberConversion
{
    public static function truncateFloat(float $number, int $decimals): float
    {
        $power = 10 ** $decimals;
        if ($number > 0) {
            return floor($number * $power) / $power;
        }

        return ceil($number * $power) / $power;
    }
}
