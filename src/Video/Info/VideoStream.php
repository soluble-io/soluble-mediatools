<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Psr\Log\LoggerInterface;
use Soluble\MediaTools\Video\Info\Util\MetadataTypeSafeReader;

class VideoStream implements VideoStreamInterface
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

    public function getWidth(): int
    {
        return $this->tsReader->getKeyIntValue('width');
    }

    public function getHeight(): int
    {
        return $this->tsReader->getKeyIntValue('height');
    }

    public function getCodedWidth(): ?int
    {
        return $this->tsReader->getKeyIntOrNullValue('coded_width');
    }

    public function getCodedHeight(): ?int
    {
        return $this->tsReader->getKeyIntOrNullValue('coded_height');
    }

    public function getSampleAspectRatio(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('sample_aspect_ratio');
    }

    public function getDisplayAspectRatio(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('display_aspect_ratio');
    }

    public function getPixFmt(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('pix_fmt');
    }

    public function getAvgFrameRate(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('avg_frame_rate');
    }

    public function getRFrameRate(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('r_frame_rate');
    }

    public function getTimeBase(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('time_base');
    }

    public function getDurationTs(): ?int
    {
        return $this->tsReader->getKeyIntOrNullValue('duration_ts');
    }

    public function getDuration(): float
    {
        return $this->tsReader->getKeyFloatValue('duration');
    }

    public function getProfile(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('profile');
    }

    public function getBitRate(): ?int
    {
        return $this->tsReader->getKeyIntOrNullValue('bit_rate');
    }

    public function getNbFrames(): ?int
    {
        return $this->tsReader->getKeyIntOrNullValue('nb_frames');
    }

    public function isAvc(): ?bool
    {
        if (!isset($this->streamMetadata['is_avc'])) {
            return null;
        }

        return $this->streamMetadata['is_avc'] === 'true';
    }

    public function getLevel(): ?int
    {
        return $this->tsReader->getKeyIntOrNullValue('level');
    }

    public function getStartTime(): ?float
    {
        return $this->tsReader->getKeyFloatOrNullValue('start_time');
    }

    public function getColorRange(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('color_range');
    }

    public function getColorSpace(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('color_space');
    }

    public function getColorTransfer(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('color_transfer');
    }

    public function getDimensions(): array
    {
        return [
            'width'  => $this->getWidth(),
            'height' => $this->getHeight()
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getTags(): array
    {
        return $this->streamMetadata['tags'] ?? [];
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
