<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;

class VideoConvertFactory
{
    public function __invoke(ContainerInterface $container): VideoConvert
    {
        return new VideoConvert(
            $container->get(FFMpegConfig::class),
            $container->get(VideoProbe::class)
        );
    }
}
