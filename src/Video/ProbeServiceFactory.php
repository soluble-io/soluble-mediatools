<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\Probe;

class ProbeServiceFactory
{
    public function __invoke(ContainerInterface $container): ProbeServiceInterface
    {
        return new Probe(
            $container->get(FFProbeConfig::class),
            $container->get(FFMpegConfig::class)
        );
    }
}
