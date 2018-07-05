<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Exception\InvalidConfigException;

class FFProbeConfigFactory
{
    /**
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container): FFProbeConfig
    {
        $key    = 'ffprobe.binary';
        $config = $container->get('config')['soluble-mediatools'] ?? [];
        if (!isset($config[$key]) || trim((string) $config[$key]) === '') {
            throw new InvalidConfigException(
                sprintf(
                    'The [\'%s\'] value is missing in config [\'soluble-mediatools\']',
                    $key
                )
            );
        }

        return new FFProbeConfig($config[$key]);
    }
}
