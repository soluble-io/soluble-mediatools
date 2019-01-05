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
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     *
     * @return array<string, int> associative array with 'height' and 'width'
     */
    public function getDimensions(int $streamIndex = 0): array;

    /**
     * Return video stream width.
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getWidth(int $streamIndex = 0): int;

    /**
     * Return video stream height.
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getHeight(int $streamIndex = 0): int;

    /**
     * Return number of frames.
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getNbFrames(int $streamIndex = 0): int;

    /**
     * Return video bitrate of the first video stream.
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getVideoBitrate(int $streamIndex = 0): int;

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getVideoCodecName(int $streamIndex = 0): ?string;

    /**
     * Return video bitrate.
     *
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getAudioBitrate(int $streamIndex = 0): int;

    /**
     * @param int $streamIndex selected a specific stream by index, default: 0 = the first available
     */
    public function getAudioCodecName(int $streamIndex = 0): ?string;

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
     * @return array<int, array>
     */
    public function getVideoStreamsMetadata(): array;

    /**
     * @throws InvalidArgumentException
     *
     * @param string $streamType any of self::SUPPORTED_STREAM_TYPES
     *
     * @return array<int, array>
     */
    public function getStreamsMetadataByType(string $streamType): array;
}
