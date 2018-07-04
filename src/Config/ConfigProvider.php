<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

use Soluble\MediaTools\VideoProbe;
use Soluble\MediaTools\VideoProbeFactory;
use Soluble\MediaTools\VideoThumb;
use Soluble\MediaTools\VideoThumbFactory;
use Soluble\MediaTools\VideoTranscode;
use Soluble\MediaTools\VideoTranscodeFactory;

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
                FFMpegConfig::class   => FFMpegConfigFactory::class,
                FFProbeConfig::class  => FFProbeConfig::class,
                VideoTranscode::class => VideoTranscodeFactory::class,
                VideoProbe::class     => VideoProbeFactory::class,
                VideoThumb::class     => VideoThumbFactory::class,
            ],
        ];
    }
}
