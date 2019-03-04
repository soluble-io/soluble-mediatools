<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Cache\CacheInterface;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Video\Logger\LoggerInterface;

class VideoInfoReaderFactory
{
    public function __invoke(ContainerInterface $container): VideoInfoReaderInterface
    {
        $logger = $container->has(LoggerInterface::class) ? $container->get(LoggerInterface::class) : null;
        $cache  = $container->has(CacheInterface::class) ? $container->get(CacheInterface::class) : null;

        return new VideoInfoReader(
            $container->get(FFProbeConfigInterface::class),
            $logger,
            $cache
        );
    }
}
