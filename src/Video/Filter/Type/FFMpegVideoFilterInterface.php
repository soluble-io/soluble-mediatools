<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter\Type;

use Soluble\MediaTools\Video\Converter\FFMpegCLIValueInterface;

interface FFMpegVideoFilterInterface extends FFMpegCLIValueInterface, VideoFilterInterface
{
}
