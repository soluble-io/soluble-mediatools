<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Exception\InvalidConfigException;

class FFMpegConfigFactory
{
    /**
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container): FFMpegConfig
    {
        $key    = 'ffmpeg.binary';
        $config = $container->get('config')['soluble-mediatools'] ?? [];

        if (!isset($config[$key]) || trim((string) $config[$key]) === '') {
            throw new InvalidConfigException(
                sprintf(
                    'The [\'%s\'] value is missing in config [\'soluble-mediatools\']',
                    $key
                )
            );
        }
        $threads     = $config['conversion.threads'] ?? null;
        $timeout     = $config['conversion.timeout'] ?? null;
        $idleTimeout = $config['conversion.idle_timeout'] ?? null;

        return new FFMpegConfig($config[$key], $threads, $timeout, $idleTimeout);
    }
}
