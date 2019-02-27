<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Util;

trait DirLocatorTrait
{
    public function getBaseTestDirectory(): string
    {
        return dirname(__DIR__, 1);
    }

    public function getDataTestDirectory(): string
    {
        return dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'data';
    }
}
