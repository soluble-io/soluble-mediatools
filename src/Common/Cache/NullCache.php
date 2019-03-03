<?php

declare(strict_types=1);

/**
 * NullCache convenience object taken from symfony/cache. Adapted for
 * PHP7.1 strict_types, original author Nicolas Grekas <p@tchwork.com>.
 *
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * NullCache convenience object taken from symfony/cache. Adapted for
 * PHP7.1 strict_types, original author Nicolas Grekas <p@tchwork.com>.
 */
class NullCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function getMultiple($keys, $default = null)
    {
        foreach ($keys as $key) {
            yield $key => $default;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($key): bool
    {
        return false;
    }

    public function clear(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return false;
    }
}
