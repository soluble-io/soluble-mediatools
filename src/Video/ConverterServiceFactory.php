<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;

class ConverterServiceFactory
{
    public function __invoke(ContainerInterface $container): ConverterServiceInterface
    {
        return new ConverterService(
            $container->get(FFMpegConfig::class),
            $container->get(ProbeService::class)
        );
    }
}
