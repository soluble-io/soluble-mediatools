<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Cache;

use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;
use Soluble\MediaTools\Common\Cache\NullCache;

class NullCacheFactory
{
    public function __invoke(ContainerInterface $container): PsrCacheInterface
    {
        return new NullCache();
    }
}
