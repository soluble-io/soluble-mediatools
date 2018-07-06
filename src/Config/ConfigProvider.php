<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Probe;
use Soluble\MediaTools\Video\ConverterServiceFactory;
use Soluble\MediaTools\Video\ConverterServiceInterface;
use Soluble\MediaTools\Video\ProbeServiceFactory;
use Soluble\MediaTools\Video\ProbeServiceInterface;
use Soluble\MediaTools\VideoConverter;
use Soluble\MediaTools\VideoThumb;
use Soluble\MediaTools\VideoThumbFactory;

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
                VideoConverter::class => ConverterServiceInterface::class,
                Probe::class          => ProbeServiceInterface::class,
            ],
            'factories' => [
                // FFMpeg stuff
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfigFactory::class,

                // Services classes
                ConverterServiceInterface::class => ConverterServiceFactory::class,
                ProbeServiceInterface::class     => ProbeServiceFactory::class,

                VideoThumb::class     => VideoThumbFactory::class,
            ],
        ];
    }

    public function getDefaultConfiguration(): array
    {
        return require dirname(__DIR__, 2) . '/config/soluble-mediatools.global.php';
    }
}
