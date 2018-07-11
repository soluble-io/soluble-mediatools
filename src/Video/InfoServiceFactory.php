<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Video\Config\LoggerConfigInterface;

class InfoServiceFactory
{
    public function __invoke(ContainerInterface $container): InfoServiceInterface
    {
        if ($container->has(LoggerConfigInterface::class)) {
            $logger = $container->get(LoggerConfigInterface::class)->getLogger();
        } else {
            $logger = new NullLogger();
        }

        return new InfoService(
            $container->get(FFProbeConfigInterface::class),
            $logger
        );
    }
}
