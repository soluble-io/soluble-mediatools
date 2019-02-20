<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface AudioStreamCollectionInterface extends StreamCollectionInterface
{
    public function getFirst(): AudioStreamInterface;
}
