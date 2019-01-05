<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video\Adapter\Validator;

use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Adapter\Validator\FFMpegParamValidator;
use Soluble\MediaTools\Video\VideoConvertParams;

class FFMpegParamValidatorTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testNullCRFPass(): void
    {
        $params    = new VideoConvertParams();
        $validator = new FFMpegParamValidator($params);
        $validator->validate();
        self::assertTrue(true);
    }
}
