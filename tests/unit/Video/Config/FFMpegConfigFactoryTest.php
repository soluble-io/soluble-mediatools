<?php

declare(strict_types=1);

namespace MediaToolsTest\Video\Config;

use MediaToolsTest\Util\ContainerUtilTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Config\FFMpegConfigFactory;

class FFMpegConfigFactoryTest extends TestCase
{
    use ContainerUtilTrait;

    public function testSupportCustomEntryName(): void
    {
        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                'ffmpeg.binary' => '/bin/ffmpeg',
            ],
        ], 'MyCustomEntryName');

        $config = (new FFMpegConfigFactory('MyCustomEntryName'))->__invoke($container);
        self::assertEquals('/bin/ffmpeg', $config->getBinary());
    }

    public function testSupportNullConfigKey(): void
    {
        $container = $this->createZendServiceManager(
            [
                'ffmpeg.binary' => '/bin/ffmpeg',
            ],
            'config'
        );

        $config = (new FFMpegConfigFactory('config', null))->__invoke($container);
        self::assertEquals('/bin/ffmpeg', $config->getBinary());
    }

    public function testSupportCustomConfigKey(): void
    {
        $container = $this->createZendServiceManager(
            [
                'customKey' => [
                    'ffmpeg.binary' => '/bin/ffmpeg',
                ],
            ],
            'config'
        );

        $config = (new FFMpegConfigFactory('config', 'customKey'))->__invoke($container);
        self::assertEquals('/bin/ffmpeg', $config->getBinary());
    }

    public function testMustReturnFFMpegConfigWithDefaults(): void
    {
        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                //'ffmpeg.binary'       => 'ffmpeg',
                //'ffmpeg.threads'      => null,
                //'ffmpeg.timeout'      => null,
                //'ffmpeg.idle_timeout' => 60,
                //'ffmpeg.env'          => [],
            ],
        ]);

        $config = (new FFMpegConfigFactory())->__invoke($container);
        self::assertEquals(FFMpegConfig::getPlatformDefaultBinary(), $config->getBinary());
        self::assertEquals(FFMpegConfig::DEFAULT_THREADS, $config->getThreads());
        self::assertEquals(FFMpegConfig::DEFAULT_TIMEOUT, $config->getProcessParams()->getTimeout());
        self::assertEquals(FFMpegConfig::DEFAULT_IDLE_TIMEOUT, $config->getProcessParams()->getIdleTimeout());
        self::assertEquals(FFMpegConfig::DEFAULT_ENV, $config->getProcessParams()->getEnv());
    }

    public function testMustReturnFFMpegConfig(): void
    {
        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                'ffmpeg.binary'       => 'hello',
                'ffmpeg.threads'      => 10,
                'ffmpeg.timeout'      => 200,
                'ffmpeg.idle_timeout' => 50,
                'ffmpeg.env'          => ['cool' => 'test'],
            ],
        ]);

        $config = (new FFMpegConfigFactory())->__invoke($container);
        self::assertEquals('hello', $config->getBinary());
        self::assertEquals(10, $config->getThreads());
        self::assertEquals(200, $config->getProcessParams()->getTimeout());
        self::assertEquals(50, $config->getProcessParams()->getIdleTimeout());
        self::assertEquals(['cool' => 'test'], $config->getProcessParams()->getEnv());
    }

    public function testMustThrowInvalidConfigExceptionWhenInvalidType(): void
    {
        self::expectException(InvalidConfigException::class);

        $container = $this->createZendServiceManager([
            'soluble-mediatools' => [
                'ffmpeg.binary' => ['cool'],
            ],
        ]);

        (new FFMpegConfigFactory())->__invoke($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenNoConfig(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = $this->createZendServiceManager([]);

        (new FFMpegConfigFactory())($container);
    }

    public function testMustThrowInvalidConfigExceptionWhenNoEntry(): void
    {
        self::expectException(InvalidConfigException::class);
        $container = $this->createZendServiceManager([]);

        (new FFMpegConfigFactory('cool'))($container);
    }
}
