<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFProbeConfigInterface;
use Soluble\MediaTools\VideoInfoService;

class InfoServiceFactory
{
    public function __invoke(ContainerInterface $container): InfoServiceInterface
    {
        return new VideoInfoService(
            $container->get(FFProbeConfigInterface::class)
        );
    }
}
