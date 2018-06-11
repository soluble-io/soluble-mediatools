<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\ConfigTrait;

class VideoProbeFactory
{
    use ConfigTrait;

    public function __invoke(ContainerInterface $container): VideoProbe
    {
        return new VideoProbe(
            $this->getFFProbeConfig($container),
            $this->getFFMpegConfig($container)
        );
    }
}
