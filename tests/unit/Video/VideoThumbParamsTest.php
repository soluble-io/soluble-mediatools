<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\InvalidParamException;
use Soluble\MediaTools\Video\Exception\UnsetParamException;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoThumbParams;
use Soluble\MediaTools\Video\VideoThumbParamsInterface;

class VideoThumbParamsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testConstructWithParams(): void
    {
        $params = new VideoThumbParams([
            VideoThumbParamsInterface::PARAM_QUALITY_SCALE => 2,
        ]);

        self::assertSame(2, $params->getParam(VideoThumbParamsInterface::PARAM_QUALITY_SCALE));
    }

    public function testConstructWithParamsThrowsInvalidParamException(): void
    {
        $this->expectException(InvalidParamException::class);

        new VideoThumbParams([
            'not_exists' => 'mp3',
        ]);
    }

    public function testMustBeImmutable(): void
    {
        $params = new VideoThumbParams();
        self::assertCount(0, $params->toArray());
        $newParams = $params->withQualityScale(2);
        self::assertCount(0, $params->toArray());
        self::assertCount(1, $newParams->toArray());
    }

    public function testOverwriteParams(): void
    {
        $params = (new VideoThumbParams())
            ->withNoOverwrite();

        self::assertTrue($params->hasParam(VideoThumbParams::PARAM_OVERWRITE));
        self::assertFalse($params->getParam(VideoThumbParams::PARAM_OVERWRITE));

        $params = $params->withOverwrite();

        self::assertTrue($params->hasParam(VideoThumbParams::PARAM_OVERWRITE));
        self::assertTrue($params->getParam(VideoThumbParams::PARAM_OVERWRITE));
    }

    public function testWithoutParam(): void
    {
        $params = (new VideoThumbParams())
            ->withQualityScale(1)
            ->withSeekTime(new SeekTime(10.3));

        $newParams = $params->withoutParam(VideoThumbParams::PARAM_QUALITY_SCALE);
        self::assertTrue($newParams->hasParam(VideoThumbParams::PARAM_SEEK_TIME));
        self::assertFalse($newParams->hasParam(VideoThumbParams::PARAM_QUALITY_SCALE));
        self::assertTrue($params->hasParam(VideoThumbParams::PARAM_QUALITY_SCALE));
    }

    public function testHasParam(): void
    {
        $params = (new VideoThumbParams())
            ->withOutputFormat('png')
            ->withQualityScale(1);

        self::assertTrue($params->hasParam(VideoThumbParams::PARAM_QUALITY_SCALE));
        self::assertFalse($params->hasParam(VideoThumbParams::PARAM_SEEK_TIME));
        self::assertSame('png', $params->getParam(VideoThumbParams::PARAM_OUTPUT_FORMAT));
    }

    public function testWithTime(): void
    {
        $params = (new VideoThumbParams())
            ->withTime(1.423);

        self::assertTrue($params->hasParam(VideoThumbParams::PARAM_SEEK_TIME));

        /**
         * @var SeekTime
         */
        $time = $params->getParam(VideoThumbParams::PARAM_SEEK_TIME);
        self::assertEquals(1.423, $time->getTime());
    }

    public function testWithFrame(): void
    {
        $params = (new VideoThumbParams())
            ->withFrame(2);

        self::assertTrue($params->hasParam(VideoThumbParams::PARAM_WITH_FRAME));
        self::assertEquals(
            2,
            $params->getParam(VideoThumbParams::PARAM_WITH_FRAME)
        );
    }

    public function testGetParamThrowsUnsetParamException(): void
    {
        $this->expectException(UnsetParamException::class);

        $params = (new VideoThumbParams())->withTime(10);

        $params->getParam(VideoThumbParamsInterface::PARAM_QUALITY_SCALE);
    }

    public function testGetParamWillUseDefault(): void
    {
        $params  = (new VideoThumbParams())->withTime(10);
        $default = $params->getParam(VideoThumbParams::PARAM_QUALITY_SCALE, 5);
        self::assertEquals(5, $default);
    }

    public function testWithBuiltInParam(): void
    {
        $params = (new VideoThumbParams())
            ->withBuiltInParam(VideoThumbParamsInterface::PARAM_QUALITY_SCALE, 5);

        self::assertEquals([
            VideoThumbParamsInterface::PARAM_QUALITY_SCALE => 5,
        ], $params->toArray());
    }

    public function testWithParamsMustBeIdenticalToConstrutorInject(): void
    {
        $injectedParams = new VideoThumbParams([
            VideoThumbParams::PARAM_QUALITY_SCALE => 1,
        ]);

        $withParams = (new VideoThumbParams())->withQualityScale(1);

        self::assertSame($injectedParams->toArray(), $withParams->toArray());
    }
}
