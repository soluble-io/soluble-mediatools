<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Soluble\MediaTools\Common\Cache\NullCache;
use Soluble\MediaTools\Common\Exception\IOException;
use Soluble\MediaTools\Common\Exception\JsonParseException;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Info\AudioStreamCollection;
use Soluble\MediaTools\Video\Info\AudioStreamCollectionInterface;
use Soluble\MediaTools\Video\Info\SubtitleStreamCollection;
use Soluble\MediaTools\Video\Info\SubtitleStreamCollectionInterface;
use Soluble\MediaTools\Video\Info\VideoStreamCollection;
use Soluble\MediaTools\Video\Info\VideoStreamCollectionInterface;

class VideoInfo implements VideoInfoInterface
{
    /** @var array<string, mixed> */
    private $metadata;

    /** @var string */
    private $file;

    /** @var LoggerInterface */
    private $logger;

    /** @var CacheInterface */
    private $cache;

    /** @var array|null */
    private $metadataByStreamType;

    /** @var VideoStreamCollectionInterface|null; */
    private $cachedVideoStreams;

    /** @var AudioStreamCollectionInterface|null; */
    private $cachedAudioStreams;

    /** @var SubtitleStreamCollectionInterface|null; */
    private $cachedSubtitleStreams;

    /**
     * @param string               $fileName reference to filename
     * @param array                $metadata metadata as parsed from ffprobe --json
     * @param LoggerInterface|null $logger
     */
    public function __construct(string $fileName, array $metadata, ?LoggerInterface $logger = null, ?CacheInterface $cache = null)
    {
        if (!file_exists($fileName)) {
            throw new IOException(sprintf(
                'File %s does not exists',
                $this->file
            ));
        }
        $this->metadata = $metadata;
        $this->file     = $fileName;
        $this->logger   = $logger ?? new NullLogger();
        $this->cache    = $cache ?? new NullCache();
    }

    /**
     * @throws JsonParseException if json is invalid
     */
    public static function createFromFFProbeJson(string $fileName, string $ffprobeJson, ?LoggerInterface $logger = null): self
    {
        if (trim($ffprobeJson) === '') {
            throw new JsonParseException('Cannot parse empty json string');
        }
        $decoded = json_decode($ffprobeJson, true);
        if ($decoded === null) {
            throw new JsonParseException('Cannot parse json');
        }

        return new self($fileName, $decoded, $logger);
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
            $msg = sprintf('Cannot get filesize of file %s', $this->file);
            $this->logger->log(LogLevel::ERROR, $msg);
            throw new IOException($msg);
        }

        return $size;
    }

    /**
     * Return VideoStreams as a collection.
     *
     * @throws InvalidStreamMetadataException
     */
    public function getVideoStreams(): VideoStreamCollectionInterface
    {
        if ($this->cachedVideoStreams === null) {
            try {
                $videoStreamsMetadata     = array_values($this->getStreamsMetadataByType(self::STREAM_TYPE_VIDEO));
                $this->cachedVideoStreams = new VideoStreamCollection($videoStreamsMetadata);
            } catch (InvalidStreamMetadataException $e) {
                $this->logger->log(LogLevel::ERROR, sprintf(
                    'Cannot get video streams info for file: %s, message is: %s',
                    $this->file,
                    $e->getMessage()
                ));
                throw $e;
            }
        }

        return $this->cachedVideoStreams;
    }

    /**
     * Return SubtitleStreams as a collection.
     *
     * @throws InvalidStreamMetadataException
     */
    public function getSubtitleStreams(): SubtitleStreamCollectionInterface
    {
        if ($this->cachedSubtitleStreams === null) {
            try {
                $streamsMetadata             = array_values($this->getStreamsMetadataByType(self::STREAM_TYPE_SUBTITLE));
                $this->cachedSubtitleStreams = new SubtitleStreamCollection($streamsMetadata);
            } catch (InvalidStreamMetadataException $e) {
                $this->logger->log(LogLevel::ERROR, sprintf(
                    'Cannot get subtitle streams info for file: %s, message is: %s',
                    $this->file,
                    $e->getMessage()
                ));
                throw $e;
            }
        }

        return $this->cachedSubtitleStreams;
    }

    /**
     * Return VideoStreams as a collection.
     *
     * @throws InvalidStreamMetadataException
     */
    public function getAudioStreams(): AudioStreamCollectionInterface
    {
        if ($this->cachedAudioStreams === null) {
            try {
                $audioStreamsMetadata     = array_values($this->getStreamsMetadataByType(self::STREAM_TYPE_AUDIO));
                $this->cachedAudioStreams = new AudioStreamCollection($audioStreamsMetadata);
            } catch (InvalidStreamMetadataException $e) {
                $this->logger->log(LogLevel::ERROR, sprintf(
                    'Cannot get audio streams info for file: %s, message is: %s',
                    $this->file,
                    $e->getMessage()
                ));
                throw $e;
            }
        }

        return $this->cachedAudioStreams;
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

    /**
     * Return metadata as received by ffprobe.
     *
     * @return array<string, array>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Return total duration.
     */
    public function getDuration(): float
    {
        return (float) ($this->metadata['format']['duration'] ?? 0.0);
    }

    /**
     * @deprecated
     *
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
     * @deprecated
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getWidth(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['width'] ?? 0);
    }

    /**
     * @deprecated
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getHeight(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['height'] ?? 0);
    }

    /**
     * @deprecated
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getNbFrames(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['nb_frames'] ?? 0);
    }

    /**
     * @deprecated
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getVideoBitrate(int $streamIndex = 0): int
    {
        $videoStream = $this->getVideoStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($videoStream['bit_rate'] ?? 0);
    }

    /**
     * @deprecated
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getAudioBitrate(int $streamIndex = 0): int
    {
        $audioStream = $this->getAudioStreamsMetadata()[$streamIndex] ?? [];

        return (int) ($audioStream['bit_rate'] ?? 0);
    }

    /**
     * @deprecated
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getAudioCodecName(int $streamIndex = 0): ?string
    {
        $audioStream = $this->getAudioStreamsMetadata()[$streamIndex] ?? [];

        return $audioStream['codec_name'] ?? null;
    }

    /**
     * @deprecated
     *
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
     * @param string $streamType 'audio', 'video', 'subtitle', 'data' / any of self::SUPPORTED_STREAM_TYPES
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws InvalidStreamMetadataException
     */
    public function getStreamsMetadataByType(string $streamType): array
    {
        if (!in_array($streamType, self::SUPPORTED_STREAM_TYPES, true)) {
            $msg = sprintf(
                'Invalid usage, unsupported param $streamType given: %s',
                $streamType
            );
            $this->logger->log(LogLevel::ERROR, $msg);
            throw new InvalidArgumentException($msg);
        }

        return $this->getMetadataByStreamType()[$streamType];
    }

    /**
     * @throws InvalidStreamMetadataException
     */
    private function getMetadataByStreamType(): array
    {
        if ($this->metadataByStreamType === null) {
            try {
                $streams = [
                    self::STREAM_TYPE_VIDEO    => [],
                    self::STREAM_TYPE_AUDIO    => [],
                    self::STREAM_TYPE_DATA     => [],
                    self::STREAM_TYPE_SUBTITLE => [],
                ];
                if (!is_array($this->metadata['streams'] ?? null)) {
                    throw new InvalidStreamMetadataException(sprintf(
                        'Invalid or unsupported stream metadata returned by ffprobe: %s',
                        (string) json_encode($this->metadata)
                    ));
                }

                foreach ($this->metadata['streams'] as $stream) {
                    if (!is_array($stream)) {
                        throw new InvalidStreamMetadataException(sprintf(
                            'Stream metadata returned by ffprobe must be an array: %s',
                            (string) json_encode($stream)
                        ));
                    }

                    if (!isset($stream['codec_type'])) {
                        throw new InvalidStreamMetadataException(sprintf(
                            'Missing codec_type information in metadata returned by ffprobe: %s',
                            (string) json_encode($stream)
                        ));
                    }

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
                        case self::STREAM_TYPE_SUBTITLE:
                            $streams[self::STREAM_TYPE_SUBTITLE][] = $stream;
                            break;

                        default:
                            $streams[$type][] = $stream;
                    }
                }

                $this->metadataByStreamType = $streams;
            } catch (InvalidStreamMetadataException $e) {
                $this->logger->log(LogLevel::ERROR, sprintf(
                    'Cannot read metadata for file: %s. Failed with message: %s',
                    $this->file,
                    $e->getMessage()
                ));
                throw $e;
            }
        }

        return $this->metadataByStreamType;
    }
}
