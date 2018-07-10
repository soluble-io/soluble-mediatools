<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Config;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Config\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustContainsDependenciesWhenInvoked(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider->__invoke();
        self::assertArrayHasKey('dependencies', $config);

        self::assertSame($config['dependencies'], $configProvider->getDependencies());
    }

    public function testMustReturnDefaultConfiguration(): void
    {
        $configProvider = new ConfigProvider();
        $defaultConfig  = $configProvider->getDefaultConfiguration();
        self::assertArrayHasKey('soluble-mediatools', $defaultConfig);
        self::assertArrayHasKey('ffmpeg.binary', $defaultConfig['soluble-mediatools']);
        self::assertArrayHasKey('ffprobe.binary', $defaultConfig['soluble-mediatools']);
    }
}
