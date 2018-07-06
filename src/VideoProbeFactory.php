<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;

class VideoProbeFactory
{
    public function __invoke(ContainerInterface $container): Probe
    {
        return new Probe(
            $container->get(FFProbeConfig::class),
            $container->get(FFMpegConfig::class)
        );
    }
}
