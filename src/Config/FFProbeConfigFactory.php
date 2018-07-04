<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Psr\Container\ContainerInterface;

class FFProbeConfigFactory
{
    use ConfigTrait;

    public function __invoke(ContainerInterface $container): FFProbeConfig
    {
        return $this->getFFProbeConfig($container);
    }
}
