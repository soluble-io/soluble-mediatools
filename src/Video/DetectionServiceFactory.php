<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfig;

class DetectionServiceFactory
{
    public function __invoke(ContainerInterface $container): DetectionServiceInterface
    {
        return new DetectionService(
            $container->get(FFMpegConfig::class)
        );
    }
}
