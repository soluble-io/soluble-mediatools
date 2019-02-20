<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
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
            'intNull1'    => 2,
            'intNull2'    => null,
            'string'      => 'cool',
            'stringNull1' => 'test',
            'stringNull2' => null,

            'float1'     => 1,
            'float2'     => 1.12344556,
            'floatNull1' => -45.2,
            'floatNull2' => null,
        ];

        $tsReader = new MetadataTypeSafeReader($data);
        self::assertEquals(1, $tsReader->getKeyIntValue('int'));
        self::assertEquals(2, $tsReader->getKeyIntOrNullValue('intNull1'));
        self::assertNull($tsReader->getKeyIntOrNullValue('intNull2'));
        self::assertEquals('cool', $tsReader->getKeyStringValue('string'));
        self::assertEquals('test', $tsReader->getKeyStringOrNullValue('stringNull1'));
        self::assertNull($tsReader->getKeyStringOrNullValue('stringNull2'));

        self::assertEquals(1, $tsReader->getKeyFloatValue('float1'));
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
            'intNull1'    => 2,
            'intNull2'    => null,
            'string'      => 'cool',
            'stringNull1' => 'test',
            'stringNull2' => null,

            'float1'     => 1,
            'float2'     => 1.12344556,
            'floatNull1' => -45.2,
            'floatNull2' => null,
        ];

        $tsReader = new MetadataTypeSafeReader($data);

        try {
            $tsReader->getKeyIntValue('float2');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyIntValue('intNull2');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyIntOrNullValue('string');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatValue('string');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatOrNullValue('string');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyFloatValue('floatNull2');
            self::assertFalse(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyStringValue('int');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }

        try {
            $tsReader->getKeyStringOrNullValue('float1');
            self::assertTrue(true);
        } catch (UnexpectedMetadataException $e) {
            self::assertTrue(true);
        }
    }
}
