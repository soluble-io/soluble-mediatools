<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Exception\InvalidConfigException;

trait ConfigTrait
{
    protected function getFFMpegConfig(ContainerInterface $container): FFMpegConfig
    {
        $key    = 'ffmpeg.binary';
        $config = $container->get('config')['soluble-mediatools'] ?? [];

        if (!isset($config[$key]) || trim($config[$key]) === '') {
            throw new InvalidConfigException(
                sprintf(
                    'The [\'%s\'] value is missing in config [\'soluble-mediatools\']',
                    $key
                )
            );
        }
        $threads = $config['ffmpeg.threads'] ?? null;

        return new FFMpegConfig($config[$key], $threads);
    }

    protected function getFFProbeConfig(ContainerInterface $container): FFProbeConfig
    {
        $key    = 'ffprobe.binary';
        $config = $container->get('config')['soluble-mediatools'] ?? [];
        if (!isset($config[$key]) || trim($config[$key]) === '') {
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
