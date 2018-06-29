<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Exception\InvalidConfigException;

trait ConfigTrait
{
    protected function getFFProbeConfig(ContainerInterface $container): FFProbeConfig
    {
        $key    = 'ffprobe.binary';
        $config = $container->get('config')['soluble-mediatools'] ?? [];
        if (!isset($config[$key]) || count($config[$key]) === 0) {
            throw new InvalidConfigException(
                sprintf(
                    'The "%s" value is missing in config "soluble-mediatools"',
                    $key
                )
            );
        }

        return new FFProbeConfig($config[$key]);
    }

    protected function getFFMpegConfig(ContainerInterface $container): FFMpegConfig
    {
        $key    = 'ffmpeg.binary';
        $config = $container->get('config')['soluble-mediatools'] ?? [];
        if (!isset($config[$key]) || count($config[$key]) === 0) {
            throw new InvalidConfigException(
                sprintf(
                    'The "%s" value is missing in config "soluble-mediatools"',
                    $key
                )
            );
        }
        $threads = $config['ffmpeg.threads'] ?? null;

        return new FFMpegConfig($config[$key], $threads);
    }
}
