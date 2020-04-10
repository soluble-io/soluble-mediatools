<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\SeekTime;

class SeekTimeTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testCreateFormHMSs(): void
    {
        $time = SeekTime::createFromHMS('20:18:12.234');
        self::assertEquals(73092.234, $time->getTime());

        $time = SeekTime::createFromHMS('12.234');
        self::assertEquals(12.234, $time->getTime());

        $time = SeekTime::createFromHMS('1:2.234');
        self::assertEquals(62.234, $time->getTime());

        $time = SeekTime::createFromHMS('01:02.234');
        self::assertEquals(62.234, $time->getTime());

        $time = SeekTime::createFromHMS('12');
        self::assertEquals(12, $time->getTime());
    }

    public function testCreateFormHMSsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        SeekTime::createFromHMS('AA:18:12.234');
    }

    public function testConvertHMSToSeconds(): void
    {
        self::assertEquals(
            '20:18:12.234',
            SeekTime::convertSecondsToHMSs(73092.234)
        );

        self::assertEquals(
            '0:00:12.234',
            SeekTime::convertSecondsToHMSs(12.234)
        );

        self::assertEquals('0:01:02.234', SeekTime::convertSecondsToHMSs(62.234));
    }

    public function testConvertSecondsToHmsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SeekTime::convertSecondsToHMSs(-10.2);
    }

    public function testConvertHMSToSecondsThrowsException1(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SeekTime::convertHMSmToSeconds('12:45:60.123');
    }

    public function testConvertHMSToSecondsThrowsException2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SeekTime::convertHMSmToSeconds('12:60:59.123');
    }
}
