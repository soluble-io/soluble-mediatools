<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter\Type;

use Soluble\MediaTools\Video\Adapter\FFMpegCLIValueInterface;

interface FFMpegVideoFilterInterface extends FFMpegCLIValueInterface, VideoFilterInterface
{
}
