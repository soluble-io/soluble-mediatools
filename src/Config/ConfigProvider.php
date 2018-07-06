<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\Video\VideoConverterServiceFactory;
use Soluble\MediaTools\Video\VideoConverterServiceInterface;
use Soluble\MediaTools\Video\VideoProbeServiceFactory;
use Soluble\MediaTools\Video\VideoProbeServiceInterface;
use Soluble\MediaTools\VideoConverter;
use Soluble\MediaTools\VideoProbe;
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
                VideoConverter::class => VideoConverterServiceInterface::class,
                VideoProbe::class     => VideoProbeServiceInterface::class,
            ],
            'factories' => [
                // FFMpeg stuff
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfigFactory::class,

                // Services classes
                VideoConverterServiceInterface::class => VideoConverterServiceFactory::class,
                VideoProbeServiceInterface::class     => VideoProbeServiceFactory::class,

                VideoThumb::class     => VideoThumbFactory::class,
            ],
        ];
    }

    public function getDefaultConfiguration(): array
    {
        return require dirname(__DIR__, 2) . '/config/soluble-mediatools.global.php';
    }
}
