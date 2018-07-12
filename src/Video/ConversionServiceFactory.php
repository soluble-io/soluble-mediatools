<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Config\LoggerConfigInterface;

class ConversionServiceFactory
{
    public function __invoke(ContainerInterface $container): ConversionServiceInterface
    {
        if ($container->has(LoggerConfigInterface::class)) {
            $logger = $container->get(LoggerConfigInterface::class)->getLogger();
        } else {
            $logger = new NullLogger();
        }

        return new ConversionService(
            $container->get(FFMpegConfigInterface::class),
            $logger
        );
    }
}
