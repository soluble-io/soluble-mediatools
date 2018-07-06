<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\VideoInfo;

interface VideoProbeServiceInterface
{
    public function getMediaInfo(string $file): VideoInfo;
}
