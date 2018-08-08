<?php

declare(strict_types=1);

namespace MediaToolsTest\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\UnsetParamException;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\ThumbParams;
use Soluble\MediaTools\Video\ThumbParamsInterface;

class ThumbParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testMustBeImmutable(): void
    {
        $params = new ThumbParams();
        self::assertCount(0, $params->toArray());
        $newParams = $params->withQualityScale(2);
        self::assertCount(0, $params->toArray());
        self::assertCount(1, $newParams->toArray());
    }

    public function testWithoutParam(): void
    {
        $params = (new ThumbParams())
            ->withQualityScale(1)
            ->withSeekTime(new SeekTime(10.3));

        $newParams = $params->withoutParam(ThumbParams::PARAM_QUALITY_SCALE);
        self::assertTrue($newParams->hasParam(ThumbParams::PARAM_SEEK_TIME));
        self::assertFalse($newParams->hasParam(ThumbParams::PARAM_QUALITY_SCALE));
        self::assertTrue($params->hasParam(ThumbParams::PARAM_QUALITY_SCALE));
    }

    public function testHasParam(): void
    {
        $params = (new ThumbParams())
                    ->withQualityScale(1);

        self::assertTrue($params->hasParam(ThumbParams::PARAM_QUALITY_SCALE));
        self::assertFalse($params->hasParam(ThumbParams::PARAM_SEEK_TIME));
    }

    public function testWithTime(): void
    {
        $params = (new ThumbParams())
            ->withTime(1.423);

        self::assertTrue($params->hasParam(ThumbParams::PARAM_SEEK_TIME));

        /**
         * @var SeekTime
         */
        $time = $params->getParam(ThumbParams::PARAM_SEEK_TIME);
        self::assertEquals(1.423, $time->getTime());
    }

    public function testGetParamThrowsUnsetParamException(): void
    {
        self::expectException(UnsetParamException::class);

        $params = (new ThumbParams())->withTime(10);

        $params->getParam(ThumbParamsInterface::PARAM_QUALITY_SCALE);
    }

    public function testWithBuiltInParam(): void
    {
        $params = (new ThumbParams())
            ->withBuiltInParam(ThumbParamsInterface::PARAM_QUALITY_SCALE, 5);

        self::assertEquals([
            ThumbParamsInterface::PARAM_QUALITY_SCALE      => 5,
        ], $params->toArray());
    }

    public function testWithParamsMustBeIdenticalToConstrutorInject(): void
    {
        $injectedParams = new ThumbParams([
            ThumbParams::PARAM_QUALITY_SCALE => 1,
        ]);

        $withParams = (new ThumbParams())->withQualityScale(1);

        self::assertSame($injectedParams->toArray(), $withParams->toArray());
    }
}
