<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface AudioStreamInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getStreamMetadata(): array;
}
