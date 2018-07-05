<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\VideoConvert;
use Soluble\MediaTools\VideoConvertFactory;
use Soluble\MediaTools\VideoProbe;
use Soluble\MediaTools\VideoProbeFactory;
use Soluble\MediaTools\VideoThumb;
use Soluble\MediaTools\VideoThumbFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                // FFMpeg stuff
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfigFactory::class,

                // Services classes
                VideoConvert::class   => VideoConvertFactory::class,
                VideoProbe::class     => VideoProbeFactory::class,
                VideoThumb::class     => VideoThumbFactory::class,
            ],
        ];
    }
}
