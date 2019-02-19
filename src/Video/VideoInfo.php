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
use Soluble\MediaTools\Video\Info\VideoStreamCollection;

class VideoInfo implements VideoInfoInterface
{
    /** @var array<string, mixed> */
    private $metadata;

    /** @var string */
    private $file;

    /** @var array|null */
    private $metadataByStreamType;

    /**
     * @var VideoStreamCollection|null;
     */
    private $cachedVideoStreams;

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

    public function getVideoStreams(): VideoStreamCollection
    {
        if ($this->cachedVideoStreams === null) {
            $videoStreamsMetadata     = array_values($this->getStreamsMetadataByType(self::STREAM_TYPE_VIDEO));
            $this->cachedVideoStreams = new VideoStreamCollection($videoStreamsMetadata);
        }

        return $this->cachedVideoStreams;
    }

    /**
     * Format name as returned by ffprobe.
     */
    public function getFormatName(): string
    {
        return $this->metadata['format']['format_name'];
    }

    /**
     * @param string $streamType any of self::SUPPORTED_STREAM_TYPES
     */
    public function countStreams(?string $streamType = null): int
    {
        if ($streamType === null) {
            return count($this->metadata['streams'] ?? []);
        }

        return count($this->getStreamsMetadataByType($streamType));
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
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     *
     * @return array<string, int> associative array with 'height' and 'width'
     */
    public function getDimensions(int $streamIndex = 0): array
    {
        return [
            'width'  => $this->getWidth($streamIndex),
            'height' => $this->getHeight($streamIndex),
        ];
    }

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getWidth(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['width'] ?? 0);
    }

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getHeight(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['height'] ?? 0);
    }

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getNbFrames(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['nb_frames'] ?? 0);
    }

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getVideoBitrate(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['bit_rate'] ?? 0);
    }

    /**
     * Convenience method to get audio stream bitrate.
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getAudioBitrate(int $streamIndex = 0): int
    {
        $audioStream = $this->getAudioStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($audioStream['bit_rate'] ?? 0);
    }

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getAudioCodecName(int $streamIndex = 0): ?string
    {
        $audioStream = $this->getAudioStreamsMetadata()[$streamIndex] ?? [];

        return $audioStream['codec_name'] ?? null;
    }

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getVideoCodecName(int $streamIndex = 0): ?string
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return $videoStream['codec_name'] ?? null;
    }

    /**
     * @return array<int, array>
     */
    public function getAudioStreamsMetadata(): array
    {
        return array_values($this->getStreamsMetadataByType(self::STREAM_TYPE_AUDIO));
    }

    /**
     * @return array<int, array>
     */
    public function getVideoStreamsMetadata(): array
    {
        return array_values($this->getStreamsMetadataByType(self::STREAM_TYPE_VIDEO));
    }

    /**
     * @throws InvalidArgumentException
     *
     * @param string $streamType  any of self::SUPPORTED_STREAM_TYPES
     * @param int    $streamIndex selected a specific stream by index, default: 0 = the first available
     *
     * @return array<string|int, array>
     */
    public function getStreamsMetadataByType(string $streamType, int $streamIndex = 0): array
    {
        if (!in_array($streamType, self::SUPPORTED_STREAM_TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid usage, unsupported param $streamType given: %s',
                $streamType
            ));
        }

        return $this->getMetadataByStreamType()[$streamType];
    }

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
                        $streams[$type][] = $stream;
                }
            }
            $this->metadataByStreamType = $streams;
        }

        return $this->metadataByStreamType;
    }
}
