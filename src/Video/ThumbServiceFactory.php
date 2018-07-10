<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;

class ThumbServiceFactory
{
    public function __invoke(ContainerInterface $container): ThumbServiceInterface
    {
        return new ThumbService(
            $container->get(FFMpegConfigInterface::class)
        );
    }
}
