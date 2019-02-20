<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface AudioStreamInterface extends StreamInterface
{
    public function getStartTime(): ?float;
}
