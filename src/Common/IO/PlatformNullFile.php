<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\IO;

class PlatformNullFile
{
    public const PLATFORM_LINUX = 'LINUX';
    public const PLATFORM_WIN   = 'WINDOWS';

    public const SUPPORTED_PLATFORMS = [
        self::PLATFORM_LINUX,
        self::PLATFORM_WIN,
    ];

    /** @var string $platform if null platform wil be auto detected */
    protected $platform;

    /**
     * @param null|string $platform if null platform will be autodetected
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(?string $platform = null)
    {
        if ($platform === null) {
            $platform = self::getCurrentPlatform();
        } else {
            if (in_array(mb_strtoupper($platform), self::SUPPORTED_PLATFORMS, true)) {
                throw new \InvalidArgumentException(sprintf(
                    'Platform \'%s\' is not supported',
                    $platform
                ));
            }
        }
        $this->platform = mb_strtoupper($platform);
    }

    public static function getCurrentPlatform(): string
    {
        return defined('PHP_WINDOWS_VERSION_MAJOR')
                    ? self::PLATFORM_WIN : self::PLATFORM_LINUX;
    }

    /**
     * Return /dev/null on linux/unix/mac or NUL on windows
     */
    public function getNullFile(): string
    {
        switch ($this->platform) {
            case self::PLATFORM_WIN:
                return 'NUL';
            // All others for now
            case self::PLATFORM_LINUX:
            default:
                return '/dev/null';
        }
    }
}
