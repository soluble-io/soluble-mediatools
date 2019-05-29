<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Exception\NoStreamException;

final class VideoStreamCollection implements VideoStreamCollectionInterface
{
    /** @var array<int, array> */
    private $streamsMetadata;

    /** @var array<int, VideoStreamInterface> */
    private $streams;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param array<int, array> $videoStreamsMetadata
     *
     * @throws InvalidStreamMetadataException
     */
    public function __construct(array $videoStreamsMetadata, ?LoggerInterface $logger = null)
    {
        $this->streamsMetadata = $videoStreamsMetadata;
        $this->logger          = $logger ?? new NullLogger();

        $this->loadStreams();
    }

    /**
     * @throws NoStreamException
     */
    public function getFirst(): VideoStreamInterface
    {
        if ($this->count() === 0) {
            throw new NoStreamException('Unable to get video first stream, none exists');
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

    /**
     * @throws InvalidStreamMetadataException
     */
    private function loadStreams(): void
    {
        $this->streams = [];

        try {
            foreach ($this->streamsMetadata as $idx => $metadata) {
                if (!is_array($metadata)) {
                    throw new InvalidStreamMetadataException(sprintf(
                        'Invalid or unsupported metadata stream received %s',
                        (string) json_encode($metadata)
                    ));
                }
                $this->streams[] = new VideoStream($metadata);
            }
        } catch (InvalidStreamMetadataException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());
            throw $e;
        }
    }
}
