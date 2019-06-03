<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Filter;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\ParamValidationException;
use Soluble\MediaTools\Video\Filter\ScaleFilter;

class ScaleFilterTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testGetFFMpegCLIValue(): void
    {
        self::assertEquals(
            'scale=w=1024:h=800',
            (new ScaleFilter(1024, 800))->getFFmpegCLIValue()
        );

        self::assertEquals(
            'scale=w=1024:h=800:force_original_aspect_ratio=decrease',
            (new ScaleFilter(1024, 800, ScaleFilter::OPTION_ASPECT_RATIO_DECREASE))
            ->getFFmpegCLIValue()
        );

        self::assertEquals(
            'scale=w=1024:h=800:force_original_aspect_ratio=increase',
            (new ScaleFilter(1024, 800, ScaleFilter::OPTION_ASPECT_RATIO_INCREASE))
            ->getFFmpegCLIValue()
        );

        self::assertEquals(
            'scale=w=1024:h=800:eval=eval:interl=1:flags=flags:param0=1.5:param1=1.6:size=600x600',
            (new ScaleFilter(
                1024,
                800,
                null,
                'eval',
                1,
                'flags',
                1.5,
                1.6,
                '600x600'
            ))->getFFmpegCLIValue()
        );
    }

    public function testInvalidAspectRatioThrowsException(): void
    {
        $this->expectException(ParamValidationException::class);

        (new ScaleFilter(
            1024,
            800,
            'cool'
        ));
    }
}
