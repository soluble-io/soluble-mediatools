<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;

class DetectionServiceFactory
{
    public function __invoke(ContainerInterface $container): DetectionServiceInterface
    {
        return new DetectionService(
            $container->get(FFProbeConfig::class),
            $container->get(FFMpegConfig::class)
        );
    }
}
