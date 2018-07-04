<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Psr\Container\ContainerInterface;

class FFMpegConfigFactory
{
    use ConfigTrait;

    public function __invoke(ContainerInterface $container): FFMpegConfig
    {
        return $this->getFFMpegConfig($container);
    }
}
