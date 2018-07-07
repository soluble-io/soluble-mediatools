<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\VideoDetectionService;

class DetectionServiceFactory
{
    public function __invoke(ContainerInterface $container): DetectionServiceInterface
    {
        return new VideoDetectionService(
            $container->get(FFMpegConfig::class)
        );
    }
}
