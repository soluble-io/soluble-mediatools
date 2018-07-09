<?php

declare(strict_types=1);

namespace MediaToolsTest\Util;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Exception\InvalidConfigException;
use Soluble\MediaTools\Util\SafeConfigReader;

class SafeConfigReaderTest extends TestCase
{
    public function testValuesAreReturnedAsIs(): void
    {
        $config = [
            'stringKey' => 'hello',
            'boolKey'   => true,
            'intKey'    => 1,
            'arrayKey'  => ['cool' => 'test'],
        ];

        $scr = new SafeConfigReader($config);

        self::assertEquals(['cool' => 'test'], $scr->getArray('arrayKey'));
        self::assertEquals('hello', $scr->getString('stringKey'));
        self::assertEquals(1, $scr->getInt('intKey'));
        self::assertTrue($scr->getBool('boolKey'));

        self::assertEquals(['cool' => 'test'], $scr->getNullableArray('arrayKey'));
        self::assertEquals('hello', $scr->getNullableString('stringKey'));
        self::assertEquals(1, $scr->getNullableInt('intKey'));
        self::assertTrue($scr->getNullableBool('boolKey'));
    }

    public function testNullValuesAreReturnedAsNull(): void
    {
        $config = [
            'stringKey' => null,
            'boolKey'   => null,
            'intKey'    => null,
            'arrayKey'  => null,
        ];

        $scr = new SafeConfigReader($config);

        self::assertNull($scr->getNullableArray('arrayKey'));
        self::assertNull($scr->getNullableString('stringKey'));
        self::assertNull($scr->getNullableInt('intKey'));
        self::assertNull($scr->getNullableBool('boolKey'));

        $config = [];

        $scr = new SafeConfigReader($config);

        self::assertNull($scr->getNullableArray('arrayKey'));
        self::assertNull($scr->getNullableString('stringKey'));
        self::assertNull($scr->getNullableInt('intKey'));
        self::assertNull($scr->getNullableBool('boolKey'));
    }

    public function testDefaultValuesAreReturnedWhenNoConfig(): void
    {
        $config = [];

        $scr = new SafeConfigReader($config);

        self::assertEquals(['cool' => 'test'], $scr->getArray('arrayKey', ['cool' => 'test']));
        self::assertEquals('hello', $scr->getString('stringKey', 'hello'));
        self::assertEquals(1, $scr->getInt('intKey', 1));
        self::assertTrue($scr->getBool('boolKey', true));

        self::assertEquals(['cool' => 'test'], $scr->getNullableArray('arrayKey', ['cool' => 'test']));
        self::assertEquals('hello', $scr->getNullableString('stringKey', 'hello'));
        self::assertEquals(1, $scr->getNullableInt('intKey', 1));
        self::assertTrue($scr->getNullableBool('boolKey', true));
    }

    public function testIfKeyExistsNullTakesPrecedenceOverDefault(): void
    {
        $config = [
            'stringKey' => null,
            'boolKey'   => null,
            'intKey'    => null,
            'arrayKey'  => null,
        ];

        $scr = new SafeConfigReader($config);

        self::assertNull($scr->getNullableArray('arrayKey', ['cool' => 'test']));
        self::assertNull($scr->getNullableString('stringKey', 'hello'));
        self::assertNull($scr->getNullableInt('intKey', 1));
        self::assertNull($scr->getNullableBool('boolKey', true));

        // And gives exception for
        try {
            $scr->getArray('arrayKey', ['cool' => 'test']);
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        try {
            $scr->getBool('boolKey', true);
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        try {
            $scr->getInt('intKey', 1);
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        try {
            $scr->getString('stringKey', 'hello');

            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }
    }

    public function testWrongTypesMustThrowInvalidConfigException(): void
    {
        $config = [
            'stringKey' => [],
            'boolKey'   => 'hello',
            'intKey'    => 'MyGod',
            'arrayKey'  => true,
        ];

        $scr = new SafeConfigReader($config);

        // And gives exception for
        self::assertTrue($scr->keyExists('arrayKey'));
        try {
            $scr->getArray('arrayKey');
            $scr->getNullableArray('arrayKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('boolKey'));
        try {
            $scr->getBool('boolKey');
            $scr->getNullableBool('boolKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('intKey'));
        try {
            $scr->getInt('intKey');
            $scr->getNullableInt('intKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('stringKey'));
        try {
            $scr->getNullableString('stringKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }
    }
}
