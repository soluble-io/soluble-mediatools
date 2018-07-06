<?php

declare(strict_types=1);

namespace MediaToolsTest\Config;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Config\ConfigProvider;
use Soluble\MediaTools\Config\FFProbeConfigFactory;
use Soluble\MediaTools\Exception\InvalidConfigException;
use Zend\ServiceManager\ServiceManager;

class FFProbeConfigFactoryTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustReturnFFProbeConfig(): void
    {
        self::doesNotPerformAssertions();
        $container = new ServiceManager(
            array_merge(
                [
                    'services' => [
                        'config' => (new ConfigProvider())->getDefaultConfiguration(),
                    ]],
                [] //could be: (new ConfigProvider)->getDependencies()
            )
        );
        (new FFProbeConfigFactory())($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenNoConfig(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = new ServiceManager(
            [
                'services' => ['config' => []],
            ]
        );
        (new FFProbeConfigFactory())($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenInvalidConfig(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = new ServiceManager(
            [
                'services' => [
                    'config' => [
                        'soluble-mediatools' => [
                            // nothing
                        ],
                    ],
                ],
            ]
        );
        (new FFProbeConfigFactory())($container);
    }
}
