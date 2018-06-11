<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\ConfigTrait;

class VideoTranscodeFactory
{
    use ConfigTrait;

    public function __invoke(ContainerInterface $container): VideoTranscode
    {
        return new VideoTranscode(
            $this->getFFMpegConfig($container),
            $container->get(VideoProbe::class)
        );
    }
}
