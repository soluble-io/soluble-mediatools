<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

interface VideoInfoInterface
{
    public const STREAM_TYPE_AUDIO = 'audio';
    public const STREAM_TYPE_VIDEO = 'video';
    public const STREAM_TYPE_DATA  = 'data';

    public const SUPPORTED_STREAM_TYPES = [
        self::STREAM_TYPE_AUDIO,
        self::STREAM_TYPE_VIDEO,
        self::STREAM_TYPE_DATA,
    ];

    public function getFileSize(): int;

    /**
     * Return the file container format name.
     */
    public function getFormatName(): string;

    /**
     * Return the number of streams.
     */
    public function countStreams(): int;

    /**
     * Return original file path.
     */
    public function getFile(): string;

    /**
     * Return total duration in seconds (decimals shows milliseconds).
     */
    public function getDuration(): float;

    /**
     * @return array<string, int> associative array with 'height' and 'width'
     */
    public function getDimensions(): array;

    /**
     * Return first video stream width.
     */
    public function getWidth(): int;

    /**
     * Return first video stream height.
     */
    public function getHeight(): int;

    public function getNbFrames(): int;

    /**
     * Return video bitrate of the first video stream.
     */
    public function getVideoBitrate(): int;

    /**
     * Return video bitrate of the first video stream.
     */
    public function getAudioBitrate(): int;

    /**
     * Return underlying ffprobe data.
     */
    public function getMetadata(): array;

    /**
     * @param string $streamType any of self::SUPPORTED_STREAM_TYPES
     *
     * @return array<string, mixed>
     */
    public function getStreamMetadataByType(string $streamType): array;
}
