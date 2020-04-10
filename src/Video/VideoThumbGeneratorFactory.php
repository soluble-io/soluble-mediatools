<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Logger\LoggerInterface;

final class VideoThumbGeneratorFactory
{
    public function __invoke(ContainerInterface $container): VideoThumbGeneratorInterface
    {
        $logger = $container->has(LoggerInterface::class) ? $container->get(LoggerInterface::class) : null;

        return new VideoThumbGenerator(
            $container->get(FFMpegConfigInterface::class),
            $logger
        );
    }
}
