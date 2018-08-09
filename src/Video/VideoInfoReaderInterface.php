<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

interface VideoInfoReaderInterface
{
    public function query(string $file): VideoInfo;
}
