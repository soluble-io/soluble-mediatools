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
    /**
     * @return array<string, array<string,array<string,mixed>>>
     */
    public function __invoke(): array
    {
        return [
            'services' => [
                'config' => $this->getDefaultConfiguration(),
            ],
            'dependencies' => $this->getDependencies()
        ];
    }

    /**
     * @return array<string, array<string,string>>
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => $this->getAliases(),
            'factories' => $this->getFactories()
        ];
    }

    /**
     * Return concrete implementation aliases if needed
     *
     * @return array<string, string>
     */
    public function getAliases(): array {

        return [
            // Configuration holders
            FFMpegConfig::class           => FFMpegConfigInterface::class,
            FFProbeConfig::class          => FFProbeConfigInterface::class,

            // Services
            VideoConversionService::class => ConversionServiceInterface::class,
            VideoInfoService::class       => InfoServiceInterface::class,
            VideoDetectionService::class  => DetectionServiceInterface::class,
            VideoThumbService::class      => ThumbServiceInterface::class,
        ];
    }

    /**
     * Return interface based factories
     *
     * @return array<string, string>
     */
    public function getFactories(): array {
        return [
            // Configuration holders
            FFMpegConfigInterface::class   => FFMpegConfigFactory::class,
            FFProbeConfigInterface::class  => FFProbeConfigFactory::class,

            // Services classes
            ConversionServiceInterface::class => ConversionServiceFactory::class,
            InfoServiceInterface::class       => InfoServiceFactory::class,
            DetectionServiceInterface::class  => DetectionServiceFactory::class,
            ThumbServiceInterface::class      => ThumbServiceFactory::class,
        ];
    }

    /**
     * @return array<string, array<string,mixed>>
     */
    public function getDefaultConfiguration(): array
    {
        return require dirname(__DIR__, 2) . '/config/soluble-mediatools.global.php';
    }
}
