<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface SubtitleStreamCollectionInterface extends StreamCollectionInterface
{
    public function getFirst(): SubtitleStreamInterface;
}
