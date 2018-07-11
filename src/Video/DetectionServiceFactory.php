<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Config\LoggerConfigInterface;

class DetectionServiceFactory
{
    public function __invoke(ContainerInterface $container): DetectionServiceInterface
    {
        if ($container->has(LoggerConfigInterface::class)) {
            $logger = $container->get(LoggerConfigInterface::class)->getLogger();
        } else {
            $logger = new NullLogger();
        }

        return new DetectionService(
            $container->get(FFMpegConfig::class),
            $logger
        );
    }
}
