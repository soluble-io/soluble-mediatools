<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Common\Config;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Config\SafeConfigReader;
use Soluble\MediaTools\Common\Exception\InvalidConfigException;

class SafeConfigReaderTest extends TestCase
{
    public function testValuesAreReturnedAsIs(): void
    {
        $config = [
            'stringKey' => 'hello',
            'boolKey'   => true,
            'intKey'    => 1,
            'floatKey'  => 1.1,
            'arrayKey'  => ['cool' => 'test'],
        ];

        $scr = new SafeConfigReader($config);

        self::assertEquals(['cool' => 'test'], $scr->getArray('arrayKey'));
        self::assertEquals('hello', $scr->getString('stringKey'));
        self::assertEquals(1, $scr->getInt('intKey'));
        self::assertEquals(1.1, $scr->getFloat('floatKey'));
        self::assertTrue($scr->getBool('boolKey'));

        self::assertEquals(['cool' => 'test'], $scr->getNullableArray('arrayKey'));
        self::assertEquals('hello', $scr->getNullableString('stringKey'));
        self::assertEquals(1, $scr->getNullableInt('intKey'));
        self::assertEquals(1.1, $scr->getNullableFloat('floatKey'));
        self::assertTrue($scr->getNullableBool('boolKey'));
    }

    public function testNullValuesAreReturnedAsNull(): void
    {
        $config = [
            'stringKey' => null,
            'boolKey'   => null,
            'intKey'    => null,
            'arrayKey'  => null,
            'floatKey'  => null,
        ];

        $scr = new SafeConfigReader($config);

        self::assertNull($scr->getNullableArray('arrayKey'));
        self::assertNull($scr->getNullableString('stringKey'));
        self::assertNull($scr->getNullableInt('intKey'));
        self::assertNull($scr->getNullableFloat('floatKey'));
        self::assertNull($scr->getNullableBool('boolKey'));

        $config = [];

        $scr = new SafeConfigReader($config);

        self::assertNull($scr->getNullableArray('arrayKey'));
        self::assertNull($scr->getNullableString('stringKey'));
        self::assertNull($scr->getNullableInt('intKey'));
        self::assertNull($scr->getNullableFloat('floatKey'));
        self::assertNull($scr->getNullableBool('boolKey'));
    }

    public function testEnsureKeyExists(): void
    {
        $scr = new SafeConfigReader(['test' => 1]);
        $scr->ensureKeyExists('test');
        self::assertTrue(true);

        $this->expectException(InvalidConfigException::class);
        $scr->ensureKeyExists('cool');
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
            'floatKey'  => 'Pouf',
            'floatInt'  => 1,
            'arrayKey'  => true,
        ];

        $scr = new SafeConfigReader($config);

        // And gives exception for
        self::assertTrue($scr->keyExists('arrayKey'));

        try {
            $scr->getArray('arrayKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('boolKey'));

        try {
            $scr->getBool('boolKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('intKey'));

        try {
            $scr->getInt('intKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('floatKey'));

        try {
            $scr->getFloat('floatKey');
            self::fail('Default cannot be taken if the config key exists and is null');
        } catch (InvalidConfigException $e) {
        }

        self::assertTrue($scr->keyExists('floatInt'));

        try {
            $scr->getFloat('floatInt', null, false);
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
