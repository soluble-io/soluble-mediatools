<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Psr\Log\LoggerInterface;
use Soluble\MediaTools\Video\Info\Util\MetadataTypeSafeReader;

class AudioStream implements AudioStreamInterface
{
    /** @var array<string, mixed> */
    private $streamMetadata;

    /** @var MetadataTypeSafeReader */
    private $tsReader;

    public function __construct(array $streamMetadata, ?LoggerInterface $logger = null)
    {
        $this->streamMetadata = $streamMetadata;
        $this->tsReader       = new MetadataTypeSafeReader($streamMetadata, $logger);
    }

    /**
     * Return underlying ffprobe json metadata.
     *
     * @return array<string, mixed>
     */
    public function getStreamMetadata(): array
    {
        return $this->streamMetadata;
    }
}
