<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Logger;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;

class FileLoggerFactory
{
    /**
     * @throws InvalidConfigException
     */
    public function __invoke(?ContainerInterface $container = null): LoggerInterface
    {
        $logger = new \Monolog\Logger('soluble-mediatools-cli');
        $logger->pushHandler(
            new \Monolog\Handler\StreamHandler(
                '/tmp/aaaa.log',
                \Monolog\Logger::INFO
            )
        );

        return $logger;
    }
}
