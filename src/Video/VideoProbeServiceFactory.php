<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\VideoProbe;

class VideoProbeServiceFactory
{
    public function __invoke(ContainerInterface $container): VideoProbeServiceInterface
    {
        return new VideoProbe(
            $container->get(FFProbeConfig::class),
            $container->get(FFMpegConfig::class)
        );
    }
}
