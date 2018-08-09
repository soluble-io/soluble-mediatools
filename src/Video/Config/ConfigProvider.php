<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Video\VideoConverter;
use Soluble\MediaTools\Video\ConversionServiceFactory;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\DetectionService;
use Soluble\MediaTools\Video\DetectionServiceFactory;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Soluble\MediaTools\Video\InfoService;
use Soluble\MediaTools\Video\InfoServiceFactory;
use Soluble\MediaTools\Video\InfoServiceInterface;
use Soluble\MediaTools\Video\ThumbService;
use Soluble\MediaTools\Video\ThumbServiceFactory;
use Soluble\MediaTools\Video\ThumbServiceInterface;

class ConfigProvider
{
    /**
     * @return array<string, array<string,array<string,mixed>>>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array<string, array<string,string>>
     */
    public function getDependencies(): array
    {
        return [
            'aliases'   => $this->getAliases(),
            'factories' => $this->getFactories(),
        ];
    }

    /**
     * Return concrete implementation aliases if needed.
     *
     * @return array<string, string>
     */
    public function getAliases(): array
    {
        return [
            // Configuration holders
            FFMpegConfig::class           => FFMpegConfigInterface::class,
            FFProbeConfig::class          => FFProbeConfigInterface::class,

            // Services
            VideoConverter::class      => VideoConverterInterface::class,
            InfoService::class            => InfoServiceInterface::class,
            DetectionService::class       => DetectionServiceInterface::class,
            ThumbService::class           => ThumbServiceInterface::class,
        ];
    }

    /**
     * Return interface based factories.
     *
     * @return array<string, string>
     */
    public function getFactories(): array
    {
        return [
            // Configuration holders
            FFMpegConfigInterface::class   => FFMpegConfigFactory::class,
            FFProbeConfigInterface::class  => FFProbeConfigFactory::class,

            // Services classes
            VideoConverterInterface::class => ConversionServiceFactory::class,
            InfoServiceInterface::class       => InfoServiceFactory::class,
            DetectionServiceInterface::class  => DetectionServiceFactory::class,
            ThumbServiceInterface::class      => ThumbServiceFactory::class,

            // Logger
            //LoggerConfigInterface::class    => <Factory to create / too much choice>
        ];
    }

    /**
     * @return array<string, array<string,mixed>>
     */
    public function getDefaultConfiguration(): array
    {
        return require dirname(__DIR__, 3) . '/config/soluble-mediatools.config.php';
    }
}
