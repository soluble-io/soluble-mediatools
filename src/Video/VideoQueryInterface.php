<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

interface VideoQueryInterface
{
    public function query(string $file): VideoInfo;
}
