<?php

declare(strict_types=1);

namespace MediaToolsTest\Config;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Config\ConfigProvider;
use Soluble\MediaTools\Config\FFMpegConfigFactory;
use Soluble\MediaTools\Exception\InvalidConfigException;
use Zend\ServiceManager\ServiceManager;

class FFMpegConfigFactoryTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustReturnFFMpegConfig(): void
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
        (new FFMpegConfigFactory())($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenNoConfig(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = new ServiceManager(
            [
                'services' => ['config' => []],
            ]
        );
        (new FFMpegConfigFactory())($container);
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
        (new FFMpegConfigFactory())($container);
    }
}
