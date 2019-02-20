<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Soluble\MediaTools\Video\Exception\StreamNotFoundException;

class VideoStreamCollection implements StreamCollectionInterface
{
    /** @var array<int, array> */
    private $streamsMetadata;

    /** @var array<int, VideoStream> */
    private $streams;

    /**
     * @param array<int, array> $videoStreamsMetadata
     */
    public function __construct(array $videoStreamsMetadata)
    {
        $this->streamsMetadata = $videoStreamsMetadata;
        $this->loadStreams();
    }

    public function getFirst(): VideoStream
    {
        if ($this->count() === 0) {
            throw new StreamNotFoundException('Unable to get video first stream, none exists');
        }

        return new VideoStream($this->streamsMetadata[0]);
    }

    public function count(): int
    {
        return count($this->streamsMetadata);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->streams);
    }

    private function loadStreams(): void
    {
        $this->streams = [];
        foreach ($this->streamsMetadata as $idx => $metadata) {
            $this->streams[] = new VideoStream($metadata);
        }
    }
}
