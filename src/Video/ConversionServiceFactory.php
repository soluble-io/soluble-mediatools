<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfig;

class ConversionServiceFactory
{
    public function __invoke(ContainerInterface $container): ConversionServiceInterface
    {
        return new ConversionService(
            $container->get(FFMpegConfig::class)
        );
    }
}
