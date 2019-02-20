<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Soluble\MediaTools\Video\Info\Util\MetadataTypeSafeReader;

class VideoStream implements VideoStreamInterface
{
    /** @var array<string, mixed> */
    private $streamMetadata;

    /** @var MetadataTypeSafeReader */
    private $tsReader;

    public function __construct(array $streamMetadata)
    {
        $this->streamMetadata = $streamMetadata;
        $this->tsReader       = new MetadataTypeSafeReader($streamMetadata);
    }

    public function getIndex(): int
    {
        return $this->tsReader->getKeyIntValue('index');
    }

    public function getCodecName(): string
    {
        return $this->streamMetadata['codec_name'];
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

    public function getDisplayAspectRatio(): string
    {
        return $this->streamMetadata['display_aspect_ratio'];
    }

    public function getPixFmt(): ?string
    {
        return $this->tsReader->getKeyStringOrNullValue('pix_fmt');
    }

    public function getAvgFrameRate(): string
    {
        return $this->streamMetadata['avg_frame_rate'];
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

    public function getNbFrames(): int
    {
        return $this->tsReader->getKeyIntValue('nb_frames');
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

    /**
     * @return array<string, string>
     */
    public function getTags(): array
    {
        return $this->streamMetadata['tags'] ?? [];
    }
}
