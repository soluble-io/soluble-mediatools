<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;

class VideoThumbFactory
{
    public function __invoke(ContainerInterface $container): VideoThumb
    {
        return new VideoThumb(
            $container->get(FFMpegConfig::class),
            $container->get(Probe::class)
        );
    }
}
