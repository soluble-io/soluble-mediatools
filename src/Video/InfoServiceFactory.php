<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFProbeConfig;
use Soluble\MediaTools\VideoInfoService;

class InfoServiceFactory
{
    public function __invoke(ContainerInterface $container): InfoServiceInterface
    {
        return new VideoInfoService(
            $container->get(FFProbeConfig::class)
        );
    }
}
