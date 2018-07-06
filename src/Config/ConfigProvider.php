<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Video\ConverterService;
use Soluble\MediaTools\Video\ConverterServiceFactory;
use Soluble\MediaTools\Video\ConverterServiceInterface;
use Soluble\MediaTools\Video\DetectionService;
use Soluble\MediaTools\Video\DetectionServiceFactory;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Soluble\MediaTools\Video\ProbeService;
use Soluble\MediaTools\Video\ProbeServiceFactory;
use Soluble\MediaTools\Video\ProbeServiceInterface;
use Soluble\MediaTools\Video\ThumbService;
use Soluble\MediaTools\Video\ThumbServiceFactory;
use Soluble\MediaTools\Video\ThumbServiceInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'services' => [
                'config' => $this->getDefaultConfiguration(),
            ],
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'aliases' => [
                ConverterService::class => ConverterServiceInterface::class,
                ProbeService::class     => ProbeServiceInterface::class,
                DetectionService::class => DetectionServiceInterface::class,
                ThumbService::class     => ThumbServiceInterface::class,
            ],
            'factories' => [
                // Configuration array for ffmpeg and ffprobe
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfigFactory::class,

                // Services classes
                ConverterServiceInterface::class => ConverterServiceFactory::class,
                ProbeServiceInterface::class     => ProbeServiceFactory::class,
                DetectionServiceInterface::class => DetectionServiceFactory::class,
                ThumbServiceInterface::class     => ThumbServiceFactory::class,
            ],
        ];
    }

    public function getDefaultConfiguration(): array
    {
        return require dirname(__DIR__, 2) . '/config/soluble-mediatools.global.php';
    }
}
