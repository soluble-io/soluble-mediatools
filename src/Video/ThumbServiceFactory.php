<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Config\LoggerConfigInterface;

class ThumbServiceFactory
{
    public function __invoke(ContainerInterface $container): ThumbServiceInterface
    {
        if ($container->has(LoggerConfigInterface::class)) {
            $logger = $container->get(LoggerConfigInterface::class)->getLogger();
        } else {
            $logger = new NullLogger();
        }

        return new ThumbService(
            $container->get(FFMpegConfigInterface::class),
            $logger
        );
    }
}
