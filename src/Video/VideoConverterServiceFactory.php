<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\VideoConvert;
use Soluble\MediaTools\VideoProbe;

class VideoConverterServiceFactory
{
    public function __invoke(ContainerInterface $container): VideoConverterServiceInterface
    {
        return new VideoConvert(
            $container->get(FFMpegConfig::class),
            $container->get(VideoProbe::class)
        );
    }
}
