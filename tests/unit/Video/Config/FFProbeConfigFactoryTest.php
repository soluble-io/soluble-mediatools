<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Config;

use MediaToolsTest\Util\ContainerUtilTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;
use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Config\FFProbeConfigFactory;

class FFProbeConfigFactoryTest extends TestCase
{
    use ContainerUtilTrait;

    public function testSupportCustomEntryName(): void
    {
        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                'ffprobe.binary' => '/bin/ffprobe',
            ],
        ], 'MyCustomEntryName');

        $config = (new FFProbeConfigFactory('MyCustomEntryName'))->__invoke($container);
        self::assertEquals('/bin/ffprobe', $config->getBinary());
    }

    public function testSupportNullConfigKey(): void
    {
        $container = $this->createZendServiceManager(
            [
                'ffprobe.binary' => '/bin/ffprobe',
            ],
            'config'
        );

        $config = (new FFProbeConfigFactory('config', null))->__invoke($container);
        self::assertEquals('/bin/ffprobe', $config->getBinary());
    }

    public function testSupportCustomConfigKey(): void
    {
        $container = $this->createZendServiceManager(
            [
                'customKey' => [
                    'ffprobe.binary' => '/bin/ffprobe',
                ],
            ],
            'config'
        );

        $config = (new FFProbeConfigFactory('config', 'customKey'))->__invoke($container);
        self::assertEquals('/bin/ffprobe', $config->getBinary());
    }

    public function testMustReturnConfigHolderWithDefaults(): void
    {
        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                //'ffprobe.binary'       => 'ffprobe',
                //'ffprobe.timeout'      => null,
                //'ffprobe.idle_timeout' => 60,
                //'ffprobe.env'          => [],
            ],
        ]);

        $config = (new FFProbeConfigFactory())->__invoke($container);
        self::assertEquals(FFProbeConfig::getPlatformDefaultBinary(), $config->getBinary());
        self::assertEquals(FFProbeConfig::DEFAULT_TIMEOUT, $config->getProcessParams()->getTimeout());
        self::assertEquals(FFProbeConfig::DEFAULT_IDLE_TIMEOUT, $config->getProcessParams()->getIdleTimeout());
        self::assertEquals(FFProbeConfig::DEFAULT_ENV, $config->getProcessParams()->getEnv());
    }

    public function testMustReturnFFMpegConfig(): void
    {
        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                'ffprobe.binary'       => 'hello',
                'ffprobe.timeout'      => 200,
                'ffprobe.idle_timeout' => 50,
                'ffprobe.env'          => ['cool' => 'test'],
            ],
        ]);

        $config = (new FFProbeConfigFactory())->__invoke($container);
        self::assertEquals('hello', $config->getBinary());
        self::assertEquals(200, $config->getProcessParams()->getTimeout());
        self::assertEquals(50, $config->getProcessParams()->getIdleTimeout());
        self::assertEquals(['cool' => 'test'], $config->getProcessParams()->getEnv());
    }

    public function testMustThrowInvalidConfigExceptionWhenInvalidType(): void
    {
        self::expectException(InvalidConfigException::class);

        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                'ffprobe.binary' => ['cool'],
            ],
        ]);

        (new FFProbeConfigFactory())->__invoke($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenNoConfig(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = $this->createZendServiceManager([]);
        (new FFProbeConfigFactory())($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenNoEntry(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = $this->createZendServiceManager([]);
        (new FFProbeConfigFactory('noentry'))($container);
    }
}
