<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Exception\IOException;
use Soluble\MediaTools\Common\Exception\JsonParseException;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\UnexpectedValueException;

class VideoInfo implements VideoInfoInterface
{
    /** @var array<string, mixed> */
    private $metadata;

    /** @var string */
    private $file;

    /** @var array|null */
    private $metadataByStreamType;

    /**
     * @param string $fileName reference to filename
     * @param array  $metadata metadata as parsed from ffprobe --json
     *
     * @throws IOException
     */
    public function __construct(string $fileName, array $metadata)
    {
        if (!file_exists($fileName)) {
            throw new IOException(sprintf(
                'File %s does not exists',
                $this->file
            ));
        }
        $this->metadata = $metadata;
        $this->file     = $fileName;
    }

    /**
     * @throws JsonParseException if json is invalid
     */
    public static function createFromFFProbeJson(string $fileName, string $ffprobeJson): self
    {
        if (trim($ffprobeJson) === '') {
            throw new JsonParseException('Cannot parse empty json string');
        }
        $decoded = json_decode($ffprobeJson, true);
        if ($decoded === null) {
            throw new JsonParseException('Cannot parse json');
        }

        return new self($fileName, $decoded);
    }

    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @throws IOException
     */
    public function getFileSize(): int
    {
        $size = @filesize($this->file);
        if ($size === false) {
            throw new IOException(sprintf(
                'Cannot get filesize of file %s',
                $this->file
            ));
        }

        return $size;
    }

    /**
     * Format name as returned by ffprobe.
     */
    public function getFormatName(): string
    {
        return $this->metadata['format']['format_name'];
    }

    public function countStreams(): int
    {
        return $this->metadata['format']['nb_streams'];
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getDuration(): float
    {
        return (float) ($this->metadata['format']['duration'] ?? 0.0);
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getDimensions(): array
    {
        return [
            'width'  => $this->getWidth(),
            'height' => $this->getHeight(),
        ];
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getWidth(): int
    {
        $videoStream = $this->getVideoStreamMetadata()[0] ?? [];

        return (int) ($videoStream['width'] ?? 0);
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getHeight(): int
    {
        $videoStream = $this->getVideoStreamMetadata()[0] ?? [];

        return (int) ($videoStream['height'] ?? 0);
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getNbFrames(): int
    {
        $videoStream = $this->getVideoStreamMetadata()[0] ?? [];

        return (int) ($videoStream['nb_frames'] ?? 0);
    }

    /**
     * Convenience method to get video bitrate.
     *
     * @throws UnexpectedValueException
     */
    public function getVideoBitrate(): int
    {
        $videoStream = $this->getVideoStreamMetadata()[0] ?? [];

        return (int) ($videoStream['bit_rate'] ?? 0);
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getAudioStreamMetadata(): array
    {
        return $this->getStreamMetadataByType(self::STREAM_TYPE_AUDIO);
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getVideoStreamMetadata(): ?array
    {
        return $this->getStreamMetadataByType(self::STREAM_TYPE_VIDEO);
    }

    /**
     * @throws UnexpectedValueException
     *
     * @param string $streamType any of self::SUPPORTED_STREAM_TYPES
     *
     * @return array<string, mixed>
     */
    public function getStreamMetadataByType(string $streamType): array
    {
        if (!in_array($streamType, self::SUPPORTED_STREAM_TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
               'Invalid usage, unsupported param $streamType given: %s',
               $streamType
            ));
        }
        $md = $this->getMetadataByStreamType();

        return $md[$streamType];
    }

    /**
     * @throws UnexpectedValueException
     */
    private function getMetadataByStreamType(): array
    {
        if ($this->metadataByStreamType === null) {
            $streams = [
                self::STREAM_TYPE_VIDEO => [],
                self::STREAM_TYPE_AUDIO => [],
                self::STREAM_TYPE_DATA  => [],
            ];
            foreach ($this->metadata['streams'] as $stream) {
                $type = mb_strtolower($stream['codec_type']);
                switch ($type) {
                    case self::STREAM_TYPE_VIDEO:
                        $streams[self::STREAM_TYPE_VIDEO][] = $stream;
                        break;
                    case self::STREAM_TYPE_AUDIO:
                        $streams[self::STREAM_TYPE_AUDIO][] = $stream;
                        break;
                    case self::STREAM_TYPE_DATA:
                        $streams[self::STREAM_TYPE_DATA][] = $stream;
                        break;
                    default:
                        throw new UnexpectedValueException(sprintf('Does not support codec_type "%s"', $type));
                }
            }
            $this->metadataByStreamType = $streams;
        }

        return $this->metadataByStreamType;
    }
}
