<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;

class InfoServiceFactory
{
    public function __invoke(ContainerInterface $container): InfoServiceInterface
    {
        return new InfoService(
            $container->get(FFProbeConfigInterface::class)
        );
    }
}
