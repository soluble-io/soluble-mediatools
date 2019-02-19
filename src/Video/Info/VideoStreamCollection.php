<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

class VideoStreamCollection implements StreamCollectionInterface
{
    /**
     * @var array<int, array>
     */
    private $streamsMetadata;

    /**
     * @param array<int, array> $videoStreamsMetadata
     */
    public function __construct(array $videoStreamsMetadata)
    {
        $this->streamsMetadata = $videoStreamsMetadata;
    }

    public function getFirst(): VideoStream
    {
        return new VideoStream($this->streamsMetadata[0]);
    }

    public function count(): int
    {
        return count($this->streamsMetadata);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->streamsMetadata);
    }
}
