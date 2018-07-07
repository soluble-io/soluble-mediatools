<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\VideoConversionService;

class ConversionServiceFactory
{
    public function __invoke(ContainerInterface $container): ConversionServiceInterface
    {
        return new VideoConversionService(
            $container->get(FFMpegConfig::class)
        );
    }
}
