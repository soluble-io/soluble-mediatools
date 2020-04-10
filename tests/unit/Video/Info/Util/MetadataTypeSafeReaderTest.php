<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Info\Util;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\UnexpectedMetadataException;
use Soluble\MediaTools\Video\Info\Util\MetadataTypeSafeReader;

class MetadataTypeSafeReaderTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testEnsureShouldPass(): void
    {
        $data = [
            'int'         => 1,
            'int2'        => '345',
            'intNull1'    => 2,
            'intNull2'    => null,
            'string'      => 'cool',
            'string2'     => 12345,
            'stringNull1' => 'test',
            'stringNull2' => null,

            'float1'     => 1,
            'float2'     => 1.12344556,
            'floatNull1' => -45.2,
            'floatNull2' => null,

            'float3' => '0.0000000',
            'float4' => '12.12345',
        ];

        $tsReader = new MetadataTypeSafeReader($data);
        self::assertEquals(1, $tsReader->getKeyIntValue('int'));

        self::assertEquals(345, $tsReader->getKeyIntValue('int2'));
        self::assertEquals(2, $tsReader->getKeyIntOrNullValue('intNull1'));
        self::assertNull($tsReader->getKeyIntOrNullValue('intNull2'));
        self::assertEquals('cool', $tsReader->getKeyStringValue('string'));
        self::assertEquals('test', $tsReader->getKeyStringOrNullValue('stringNull1'));
        self::assertNull($tsReader->getKeyStringOrNullValue('stringNull2'));
        self::assertEquals('12345', $tsReader->getKeyStringValue('string2'));

        self::assertEquals(1, $tsReader->getKeyFloatValue('float1'));
        self::assertEquals(0, $tsReader->getKeyFloatValue('float3'));
        self::assertEquals(0, $tsReader->getKeyFloatOrNullValue('float3'));
        self::assertEquals(12.12345, $tsReader->getKeyFloatOrNullValue('float4'));
        self::assertEquals(1.12344556, $tsReader->getKeyFloatOrNullValue('float2'));
        self::assertEquals(-45.2, $tsReader->getKeyFloatOrNullValue('floatNull1'));
        self::assertNull($tsReader->getKeyFloatOrNullValue('floatNull2'));
    }

    public function testEnsureInvalidKeyDoesNotProduceWarnings(): void
    {
        $tsReader = new MetadataTypeSafeReader([]);

        self::assertNull($tsReader->getKeyIntOrNullValue('cool'));
    }

    public function testEnsureShouldNotPass(): void
    {
        $data = [
            'int'         => 1,
            'int2'        => '1',
            'int3'        => '1.234.12',
            'intNull1'    => 2,
            'intNull2'    => null,
            'string'      => 'cool',
            'stringNull1' => 'test',
            'stringNull2' => null,

            'float1'     => 1,
            'float2'     => 1.12344556,
            'float3'     => '1.234.12',
            'floatNull1' => -45.2,
            'floatNull2' => null,
            'array'      => [],
        ];

        $tsReader = new MetadataTypeSafeReader($data);

        try {
            $tsReader->getKeyIntValue('int3');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatValue('float3');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyIntValue('float2');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyIntValue('intNull2');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyIntOrNullValue('string');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatValue('string');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatOrNullValue('string');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatValue('floatNull2');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyStringValue('array');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyStringOrNullValue('array');
            self::assertTrue(false);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }
    }
}
