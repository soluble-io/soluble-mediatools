<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Exception\NoStreamException;

final class SubtitleStreamCollection implements SubtitleStreamCollectionInterface
{
    /** @var array<int, array> */
    private $streamsMetadata;

    /** @var array<int, SubtitleStreamInterface> */
    private $streams;

    /**
     * @param array<int, array> $subtitleStreamsMetadata
     *
     * @throws InvalidStreamMetadataException
     */
    public function __construct(array $subtitleStreamsMetadata)
    {
        $this->streamsMetadata = $subtitleStreamsMetadata;
        $this->loadStreams();
    }

    /**
     * @throws NoStreamException
     */
    public function getFirst(): SubtitleStreamInterface
    {
        if ($this->count() === 0) {
            throw new NoStreamException('Unable to get video subtitle stream, none exists');
        }

        return new SubtitleStream($this->streamsMetadata[0]);
    }

    public function count(): int
    {
        return count($this->streamsMetadata);
    }

    /**
     * @return \ArrayIterator<int, SubtitleStreamInterface>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->streams);
    }

    /**
     * @throws InvalidStreamMetadataException
     */
    private function loadStreams(): void
    {
        $this->streams = [];
        foreach ($this->streamsMetadata as $idx => $metadata) {
            if (!is_array($metadata)) {
                throw new InvalidStreamMetadataException(sprintf(
                    'Invalid or unsupported metadata stream received %s',
                    (string) json_encode($metadata)
                ));
            }
            $this->streams[] = new SubtitleStream($metadata);
        }
    }
}
