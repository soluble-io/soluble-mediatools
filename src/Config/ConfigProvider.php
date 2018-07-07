<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Video\ConversionServiceFactory;
use Soluble\MediaTools\Video\ConversionServiceInterface;
use Soluble\MediaTools\Video\DetectionServiceFactory;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Soluble\MediaTools\Video\InfoServiceFactory;
use Soluble\MediaTools\Video\InfoServiceInterface;
use Soluble\MediaTools\Video\ThumbServiceFactory;
use Soluble\MediaTools\Video\ThumbServiceInterface;
use Soluble\MediaTools\VideoConversionService;
use Soluble\MediaTools\VideoDetectionService;
use Soluble\MediaTools\VideoInfoService;
use Soluble\MediaTools\VideoThumbService;

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
                VideoInfoService::class       => InfoServiceInterface::class,
                VideoDetectionService::class  => DetectionServiceInterface::class,
                VideoThumbService::class      => ThumbServiceInterface::class,
            ],
            'factories' => [
                // Configuration array for ffmpeg and ffprobe
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfigFactory::class,

                // Services classes
                ConversionServiceInterface::class => ConversionServiceFactory::class,
                InfoServiceInterface::class       => InfoServiceFactory::class,
                DetectionServiceInterface::class  => DetectionServiceFactory::class,
                ThumbServiceInterface::class      => ThumbServiceFactory::class,
            ],
        ];
    }

    public function getDefaultConfiguration(): array
    {
        return require dirname(__DIR__, 2) . '/config/soluble-mediatools.global.php';
    }
}
