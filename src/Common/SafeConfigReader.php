<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common;

use Soluble\MediaTools\Common\Exception\InvalidConfigException;

class SafeConfigReader
{
    /** @var null|string */
    protected $configKey;

    /** @var array<string, mixed> */
    protected $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config, ?string $configKey = null)
    {
        $this->config    = $config;
        $this->configKey = $configKey;
    }

    /**
     * @return int|null
     *
     * @throws InvalidConfigException
     */
    public function getNullableInt(string $key, ?int $default = null): ?int
    {
        $value = $this->getValueOrDefault($key, $default);
        if (!($value === null) && !is_int($value)) {
            $this->throwInvalidConfigException(sprintf(
                'Param \'%s\' must be int, \'%s\' given',
                $key,
                gettype($value)
            ), $key);
        }

        return $value;
    }

    /**
     * Check strict int.
     *
     * @throws InvalidConfigException
     */
    public function getInt(string $key, ?int $default = null): int
    {
        $value = $this->getNullableInt($key, $default);
        $this->ensureNotNull($value, $key);

        return (int) $value;
    }

    /**
     * Check strict array.
     *
     * @throws InvalidConfigException
     */
    public function getArray(string $key, ?array $default = null): array
    {
        $value = $this->getNullableArray($key, $default);
        $this->ensureNotNull($value, $key);

        return (array) $value;
    }

    /**
     * @return array|null
     *
     * @throws InvalidConfigException
     */
    public function getNullableArray(string $key, ?array $default = null): ?array
    {
        $value = $this->getValueOrDefault($key, $default);
        if (!($value === null) && !is_array($value)) {
            $this->throwInvalidConfigException(sprintf(
                'Param \'%s\' must be array, \'%s\' given',
                $key,
                gettype($value)
            ), $key);
        }

        return $value;
    }

    /**
     * Check strict string.
     *
     * @throws InvalidConfigException
     */
    public function getString(string $key, ?string $default = null): string
    {
        $value = $this->getNullableString($key, $default);
        $this->ensureNotNull($value, $key);

        return (string) $value;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getNullableString(string $key, ?string $default = null): ?string
    {
        $value = $this->getValueOrDefault($key, $default);

        if (!($value === null) && !is_string($value)) {
            $this->throwInvalidConfigException(sprintf(
                'Param \'%s\' must be string, \'%s\' given',
                $key,
                gettype($value)
            ), $key);
        }

        return $value;
    }

    /**
     * Check strict bool.
     *
     * @throws InvalidConfigException
     */
    public function getBool(string $key, ?bool $default = null): bool
    {
        $value = $this->getNullableBool($key, $default);
        $this->ensureNotNull($value, $key);

        return (bool) $value;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getNullableBool(string $key, ?bool $default = null): ?bool
    {
        $value = $this->getValueOrDefault($key, $default);
        if (!($value === null) && !is_bool($value)) {
            $this->throwInvalidConfigException(sprintf(
                'Param \'%s\' must be bool, \'%s\' given',
                $key,
                gettype($value)
            ), $key);
        }

        return $value;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function getValueOrDefault(string $key, $default)
    {
        return $this->keyExists($key) ? $this->config[$key] : $default;
    }

    public function keyExists(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * @throws InvalidConfigException
     */
    public function ensureKeyExists(string $key): void
    {
        if ($this->keyExists($key)) {
            return;
        }

        $this->throwInvalidConfigException(
            sprintf(
                'Required param [\'%s\'] is missing.',
                $key
            ),
            $key
        );
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidConfigException
     */
    protected function ensureNotNull($value, string $key): void
    {
        if ($value !== null) {
            return;
        }

        $this->throwInvalidConfigException(
            sprintf(
                'Param \'%s\' cannot be null.',
                $key
            ),
            $key
        );
    }

    protected function throwInvalidConfigException(string $msg, string $key): void
    {
        throw new InvalidConfigException(
            sprintf(
                '%s (check your config entry %s[\'%s\'])',
                $msg,
                $this->configKey === null ? '' : '[' . $this->configKey . ']',
                $key
            )
        );
    }
}
