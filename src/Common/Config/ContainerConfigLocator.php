<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Config;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;

final class ContainerConfigLocator
{
    public const DEFAULT_ENTRY_NAME = 'config';

    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $entryName;

    public function __construct(
        ContainerInterface $container,
        string $entryName = self::DEFAULT_ENTRY_NAME
    ) {
        $this->container = $container;
        $this->entryName = $entryName;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getConfig(?string $configKey = null): array
    {
        try {
            $containerConfig = $this->container->get($this->entryName);
        } catch (\Throwable $e) {
            throw new InvalidConfigException(
                sprintf('Cannot resolve container entry \'%s\' ($entryName).', $this->entryName)
            );
        }

        $config = $configKey === null ? $containerConfig : ($containerConfig[$configKey] ?? null);

        if (!is_array($config)) {
            throw new InvalidConfigException(
                sprintf('Cannot find a configuration ($entryName=%s found, invalid $configKey=%s).', $this->entryName, $configKey ?? '')
            );
        }

        return $config;
    }
}
