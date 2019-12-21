<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Config;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Common\Config\ContainerConfigLocator;
use Soluble\MediaTools\Common\Config\SafeConfigReader;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;

final class FFProbeConfigFactory
{
    public const DEFAULT_ENTRY_NAME = 'config';
    public const DEFAULT_CONFIG_KEY = 'soluble-mediatools';

    /** @var string */
    private $entryName;

    /** @var null|string */
    private $configKey;

    public function __construct(
        string $entryName = self::DEFAULT_ENTRY_NAME,
        ?string $configKey = self::DEFAULT_CONFIG_KEY
    ) {
        $this->entryName = $entryName;
        $this->configKey = $configKey;
    }

    /**
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container): FFProbeConfigInterface
    {
        $config = (new ContainerConfigLocator($container, $this->entryName))->getConfig($this->configKey);

        $scr = new SafeConfigReader($config, $this->configKey ?? '');

        return new FFProbeConfig(
            $scr->getNullableString('ffprobe.binary', null),
            $scr->getNullableFloat('ffprobe.timeout', FFProbeConfig::DEFAULT_TIMEOUT),
            $scr->getNullableFloat('ffprobe.idle_timeout', FFProbeConfig::DEFAULT_IDLE_TIMEOUT),
            $scr->getArray('ffprobe.env', FFProbeConfig::DEFAULT_ENV)
        );
    }
}
