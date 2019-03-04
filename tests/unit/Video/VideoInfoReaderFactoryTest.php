<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Common\Cache\NullCache;
use Soluble\MediaTools\Video\Cache\CacheInterface;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Video\Logger\LoggerInterface;
use Soluble\MediaTools\Video\VideoInfoReaderFactory;

class VideoInfoReaderFactoryTest extends TestCase
{
    use ServicesProviderTrait;

    public function setUp(): void
    {
    }

    public function testWithTestLogger(): void
    {
        $logger = new NullLogger();
        $cache  = new NullCache();

        $ffprobeConfig = $this->prophesize(FFProbeConfigInterface::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has(LoggerInterface::class)->willReturn(true);
        $container->get(LoggerInterface::class)->willReturn($logger);
        $container->has(CacheInterface::class)->willReturn(true);
        $container->get(CacheInterface::class)->willReturn($cache);

        $container->get(FFProbeConfigInterface::class)->willReturn($ffprobeConfig->reveal());

        $videoConverter = new VideoInfoReaderFactory();
        $videoConverter->__invoke($container->reveal());
        self::assertTrue(true);
    }
}
