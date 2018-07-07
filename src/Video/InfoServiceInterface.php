<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\VideoInfo;

interface InfoServiceInterface
{
    public function getMediaInfo(string $file): VideoInfo;
}
