<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Exception\InvalidStreamMetadataException;
use Soluble\MediaTools\Video\Info\AudioStreamCollectionInterface;
use Soluble\MediaTools\Video\Info\StreamTypeInterface;
use Soluble\MediaTools\Video\Info\VideoStreamCollectionInterface;

interface VideoInfoInterface
{
    public const STREAM_TYPE_AUDIO    = StreamTypeInterface::AUDIO;
    public const STREAM_TYPE_VIDEO    = StreamTypeInterface::VIDEO;
    public const STREAM_TYPE_DATA     = StreamTypeInterface::DATA;
    public const STREAM_TYPE_SUBTITLE = StreamTypeInterface::SUBTITLE;

    public const SUPPORTED_STREAM_TYPES = [
        self::STREAM_TYPE_AUDIO,
        self::STREAM_TYPE_VIDEO,
        self::STREAM_TYPE_SUBTITLE,
        self::STREAM_TYPE_DATA,
    ];

    public function getFileSize(): int;

    /**
     * Return the file container format name.
     */
    public function getFormatName(): string;

    /**
     * Return the number of streams.
     *
     * @param string $streamType any of self::SUPPORTED_STREAM_TYPES
     */
    public function countStreams(?string $streamType = null): int;

    /**
     * Return original file path.
     */
    public function getFile(): string;

    /**
     * Return total duration in seconds (decimals shows milliseconds).
     */
    public function getDuration(): float;

    /**
     * Return VideoStreams as a collection.
     *
     * @throws InvalidStreamMetadataException
     */
    public function getVideoStreams(): VideoStreamCollectionInterface;

    /**
     * Return VideoStreams as a collection.
     *
     * @throws InvalidStreamMetadataException
     */
    public function getAudioStreams(): AudioStreamCollectionInterface;

    /**
     * Return underlying ffprobe data.
     *
     * @return array<string, array>
     */
    public function getMetadata(): array;

    /**
     * Return underlying ffprobe audio metadata.
     *
     * @return array<int, array>
     */
    public function getAudioStreamsMetadata(): array;

    /**
     * Return underlying ffprobe video metadata.
     *
     * @return array<mixed, array>
     */
    public function getVideoStreamsMetadata(): array;

    /**
     * @throws InvalidArgumentException
     *
     * @param string $streamType 'audio'|'video'|'data'|'subtitle' (StreamTypeInterface::AUDIO, StreamTypeInterface::VIDEO, StreamTypeInterface::DATA)
     *
     * @see self::SUPPORTED_STREAM_TYPES
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStreamsMetadataByType(string $streamType): array;
}
