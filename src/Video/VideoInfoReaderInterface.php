<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

interface VideoInfoReaderInterface
{
    public function getInfo(string $file): VideoInfo;
}
