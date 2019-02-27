<?php

declare(strict_types=1);

namespace MediaToolsTest\Common\Cache;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Cache\NullCache;

class NullCacheTest extends TestCase
{
    public function createCachePool(): NullCache
    {
        return new NullCache();
    }

    public function testGetItem(): void
    {
        $cache = $this->createCachePool();
        self::assertNull($cache->get('key'));
    }

    public function testHas(): void
    {
        self::assertFalse($this->createCachePool()->has('key'));
    }

    public function testGetMultiple(): void
    {
        $cache   = $this->createCachePool();
        $keys    = ['foo', 'bar', 'baz', 'biz'];
        $default = new \stdClass();
        $items   = $cache->getMultiple($keys, $default);
        $count   = 0;
        foreach ($items as $key => $item) {
            self::assertContains($key, $keys, 'Cache key can not change.');
            self::assertSame($default, $item);
            // Remove $key for $keys
            foreach ($keys as $k => $v) {
                if ($v === $key) {
                    unset($keys[$k]);
                }
            }
            ++$count;
        }
        self::assertSame(4, $count);
    }

    public function testClear(): void
    {
        self::assertTrue($this->createCachePool()->clear());
    }

    public function testDelete(): void
    {
        self::assertTrue($this->createCachePool()->delete('key'));
    }

    public function testDeleteMultiple(): void
    {
        self::assertTrue($this->createCachePool()->deleteMultiple(['key', 'foo', 'bar']));
    }

    public function testSet(): void
    {
        $cache = $this->createCachePool();
        self::assertFalse($cache->set('key', 'val'));
        self::assertNull($cache->get('key'));
    }

    public function testSetMultiple(): void
    {
        $cache = $this->createCachePool();
        self::assertFalse($cache->setMultiple(['key' => 'val']));
        self::assertNull($cache->get('key'));
    }
}
