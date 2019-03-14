<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Psr\Log\LoggerInterface;
use Soluble\MediaTools\Video\Info\Util\MetadataTypeSafeReader;

final class SubtitleStream implements SubtitleStreamInterface
{
    /** @var array<string, mixed> */
    private $streamMetadata;

    /** @var MetadataTypeSafeReader */
    private $tsReader;

    /**
     * @param array<string, mixed> $streamMetadata
     */
    public function __construct(array $streamMetadata, ?LoggerInterface $logger = null)
    {
        $this->streamMetadata = $streamMetadata;
        $this->tsReader       = new MetadataTypeSafeReader($streamMetadata, $logger);
    }

    public function getIndex(): int
    {
        return $this->tsReader->getKeyIntValue('index');
    }

    public function getCodecType(): string
    {
        return $this->tsReader->getKeyStringValue('codec_type');
    }

    public function getCodecName(): string
    {
        return $this->tsReader->getKeyStringValue('codec_name');
    }

    public function getCodecLongName(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('codec_long_name');
    }

    public function getCodecTimeBase(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('codec_time_base');
    }

    public function getCodecTagString(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('codec_tag_string');
    }

    public function getStartTime(): ?float
    {
        return $this->tsReader->getKeyFloatOrNullValue('start_time');
    }

    public function getTimeBase(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('time_base');
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
