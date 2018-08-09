<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

interface VideoQueryInterface
{
    public function getInfo(string $file): VideoInfo;
}
