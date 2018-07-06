<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Probe;
use Soluble\MediaTools\VideoConverter;

class ConverterServiceFactory
{
    public function __invoke(ContainerInterface $container): ConverterServiceInterface
    {
        return new VideoConverter(
            $container->get(FFMpegConfig::class),
            $container->get(Probe::class)
        );
    }
}
