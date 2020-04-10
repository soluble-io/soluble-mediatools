<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Info;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Info\AspectRatio;

class AspectRatioTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testConstruct(): void
    {
        $defaultSep = AspectRatio::DEFAULT_PROPORTION_SEPARATOR;
        $ar         = new AspectRatio(16, 9, $defaultSep);
        self::assertEquals("16${defaultSep}9", $ar->__toString());

        $sep = '/';
        $ar  = new AspectRatio(16, 9, $sep);
        self::assertEquals("16${sep}9", $ar->__toString());
    }

    public function testGetString(): void
    {
        $sep = AspectRatio::DEFAULT_PROPORTION_SEPARATOR;
        $ar  = new AspectRatio(1.25, 1, $sep);

        self::assertEquals("1.25${sep}1", $ar->getString($sep));
        self::assertEquals("1.25${sep}1", $ar->getString($sep, 2));
        self::assertEquals("1.2${sep}1", $ar->getString($sep, 1));

        $ar = new AspectRatio(16, 9, $sep);
        self::assertEquals("16${sep}9", $ar->getString($sep, 2));
        self::assertEquals("16${sep}9", $ar->getString());
    }

    public function testGetXY(): void
    {
        $ar = new AspectRatio(1.25, 1.22);
        self::assertEquals(1.25, $ar->getX());
        self::assertEquals(1.22, $ar->getY());
    }

    public function testCreateFromString(): void
    {
        $sep = AspectRatio::DEFAULT_PROPORTION_SEPARATOR;
        $ar  = AspectRatio::createFromString('16/9', '/');
        self::assertEquals("16${sep}9", $ar->getString($sep, 2));
    }

    public function testCreateFromStringThrowsInvalidArgumentException1(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AspectRatio::createFromString('16:9', '/');
    }

    public function testCreateFromStringThrowsInvalidArgumentException2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AspectRatio::createFromString('16:9:3', ':');
    }

    public function testCreateFromStringThrowsInvalidArgumentException3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AspectRatio::createFromString('16.2/AA', '/');
    }

    public function testCreateFromStringThrowsInvalidArgumentException4(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AspectRatio::createFromString('-16:9', '/');
    }
}
