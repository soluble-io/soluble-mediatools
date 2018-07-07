<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Video\VideoConversionService;
use Soluble\MediaTools\Video\ConversionServiceFactory;
use Soluble\MediaTools\Video\ConversionServiceInterface;
use Soluble\MediaTools\Video\VideoDetectionService;
use Soluble\MediaTools\Video\DetectionServiceFactory;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Soluble\MediaTools\Video\VideoProbeService;
use Soluble\MediaTools\Video\ProbeServiceFactory;
use Soluble\MediaTools\Video\ProbeServiceInterface;
use Soluble\MediaTools\Video\VideoThumbService;
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
                VideoConversionService::class => ConversionServiceInterface::class,
                VideoProbeService::class     => ProbeServiceInterface::class,
                VideoDetectionService::class => DetectionServiceInterface::class,
                VideoThumbService::class     => ThumbServiceInterface::class,
            ],
            'factories' => [
                // Configuration array for ffmpeg and ffprobe
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfigFactory::class,

                // Services classes
                ConversionServiceInterface::class => ConversionServiceFactory::class,
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
