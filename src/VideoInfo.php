<?php

declare(strict_types=1);

namespace Soluble\MediaTools;

use Soluble\MediaTools\Exception\JsonParseException;
use Soluble\MediaTools\Video\InfoInterface;

class VideoInfo implements InfoInterface
{
    public const STREAM_TYPE_AUDIO = 'audio';
    public const STREAM_TYPE_VIDEO = 'video';
    public const STREAM_TYPE_DATA  = 'data';

    /** @var array */
    protected $metadata;

    /** @var string */
    protected $file;

    public function __construct(string $file, array $metadata)
    {
        $this->metadata = $metadata;
        $this->file     = $file;
    }

    public static function createFromFFProbeJson(string $file, string $ffprobeJson): self
    {
        if (trim($ffprobeJson) === '') {
            throw new JsonParseException('Cannot parse empty json string');
        }
        $decoded = json_decode($ffprobeJson, true);
        if ($decoded === null) {
            throw new JsonParseException('Cannot parse json');
        }

        return new self($file, $decoded);
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getDuration(): float
    {
        return (float) ($this->metadata['format']['duration'] ?? 0.0);
    }

    public function getDimensions(): array
    {
        $videoStream = $this->getVideoStreamInfo();

        return [
            'width'  => (int) ($videoStream['width'] ?? 0),
            'height' => (int) ($videoStream['height'] ?? 0),
        ];
    }

    public function getNbFrames(): int
    {
        $videoStream = $this->getVideoStreamInfo();

        return (int) ($videoStream['nb_frames'] ?? 0);
    }

    public function getBitrate(): int
    {
        $videoStream = $this->getVideoStreamInfo();

        return (int) ($videoStream['bit_rate'] ?? 0);
    }

    public function getAudioStreamInfo(): ?array
    {
        return $this->getStreamsByType()[self::STREAM_TYPE_AUDIO] ?? null;
    }

    public function getVideoStreamInfo(): ?array
    {
        return $this->getStreamsByType()[self::STREAM_TYPE_VIDEO] ?? null;
    }

    protected function getStreamsByType(): array
    {
        $streams = $this->metadata['streams'] ?? [];
        foreach ($streams as $stream) {
            $type = mb_strtolower($stream['codec_type']);
            switch ($type) {
                case self::STREAM_TYPE_VIDEO:
                    $streams['video'] = $stream;
                    break;
                case self::STREAM_TYPE_AUDIO:
                    $streams['audio'] = $stream;
                    break;
                case self::STREAM_TYPE_DATA:
                    $streams['data'] = $stream;
                    break;
                default:
                    throw new \Exception(sprintf('Does not support codec_type "%s"', $type));
            }
        }

        return $streams;
    }
}
