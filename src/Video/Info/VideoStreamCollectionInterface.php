<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface VideoStreamCollectionInterface extends StreamCollectionInterface
{
    public function getFirst(): VideoStreamInterface;
}
