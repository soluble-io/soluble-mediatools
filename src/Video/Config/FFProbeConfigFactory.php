<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Soluble\MediaTools\Common\Config\SafeConfigReader;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;

class FFProbeConfigFactory
{
    public const DEFAULT_ENTRY_NAME = 'config';
    public const DEFAULT_CONFIG_KEY = 'soluble-mediatools';

    /** @var string */
    protected $entryName;

    /** @var null|string */
    protected $configKey;

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
    public function __invoke(ContainerInterface $container): FFProbeConfig
    {
        try {
            $containerConfig = $container->get($this->entryName);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new InvalidConfigException(
                sprintf('Cannot resolve container entry \'%s\' ($entryName).', $this->entryName)
            );
        }

        $config = $this->configKey === null ? $containerConfig : ($containerConfig[$this->configKey] ?? null);

        if (!is_array($config)) {
            throw new InvalidConfigException(
                sprintf('Cannot find a configuration ($entryName=%s found, invalid $configKey=%s).', $this->entryName, $this->configKey)
            );
        }

        $scr = new SafeConfigReader($config, $this->configKey ?? '');

        return new FFProbeConfig(
            $scr->getNullableString('ffprobe.binary', null),
            $scr->getNullableFloat('ffprobe.timeout', FFProbeConfig::DEFAULT_TIMEOUT),
            $scr->getNullableFloat('ffprobe.idle_timeout', FFProbeConfig::DEFAULT_IDLE_TIMEOUT),
            $scr->getArray('ffprobe.env', FFProbeConfig::DEFAULT_ENV)
        );
    }
}
