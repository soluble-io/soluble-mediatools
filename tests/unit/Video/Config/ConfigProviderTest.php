<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

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

    public function testGetDefaultConfiguration(): void {
        $config = ConfigProvider::getDefaultConfiguration();
        self::assertArrayHasKey('soluble-mediatools', $config);
    }
}
