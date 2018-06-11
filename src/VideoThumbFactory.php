<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\ConfigTrait;

class VideoThumbFactory
{
    use ConfigTrait;

    public function __invoke(ContainerInterface $container): VideoThumb
    {
        return new VideoThumb(
            $this->getFFMpegConfig($container),
            $container->get(VideoProbe::class)
        );
    }
}
