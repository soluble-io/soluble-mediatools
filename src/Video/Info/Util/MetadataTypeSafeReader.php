<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info\Util;

use Soluble\MediaTools\Video\Exception\UnexpectedMetadataException;
use Webmozart\Assert\Assert;

class MetadataTypeSafeReader
{
    /**
     * @var array<string, mixed>
     */
    private $streamMetadata;

    /**
     * @param array<string, mixed> $streamMetadata
     */
    public function __construct(array $streamMetadata)
    {
        $this->streamMetadata = $streamMetadata;
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyIntValue(string $key): int
    {
        try {
            Assert::integerish(
                $this->streamMetadata[$key] ?? '',
                "The ffprobe/videoInfo metadata '$key' is expected to be an integer. Got: %s"
            );
        } catch (\Throwable $e) {
            throw new UnexpectedMetadataException($e->getMessage());
        }

        return (int) $this->streamMetadata[$key];
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyIntOrNullValue(string $key): ?int
    {
        try {
            Assert::nullOrIntegerish(
                $this->streamMetadata[$key] ?? null,
                "The ffprobe/videoInfo metadata '$key' is expected to be an integer or null. Got: %s"
            );
        } catch (\Throwable $e) {
            throw new UnexpectedMetadataException($e->getMessage());
        }
        if (isset($this->streamMetadata[$key])) {
            return (int) $this->streamMetadata[$key];
        }

        return null;
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyFloatValue(string $key): float
    {
        try {
            Assert::numeric(
                $this->streamMetadata[$key] ?? '',
                "The ffprobe/videoInfo metadata '$key' is expected to be a float. Got: %s"
            );
        } catch (\Throwable $e) {
            throw new UnexpectedMetadataException($e->getMessage());
        }

        return (float) $this->streamMetadata[$key];
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyFloatOrNullValue(string $key): ?float
    {
        try {
            Assert::nullOrNumeric(
                $this->streamMetadata[$key] ?? null,
                "The ffprobe/videoInfo metadata '$key' is expected to be a float or null. Got: %s"
            );
        } catch (\Throwable $e) {
            throw new UnexpectedMetadataException($e->getMessage());
        }
        if (isset($this->streamMetadata[$key])) {
            return (float) $this->streamMetadata[$key];
        }

        return null;
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyStringOrNullValue(string $key): ?string
    {
        try {
            Assert::nullOrString(
                $this->streamMetadata[$key] ?? null,
                "The ffprobe/videoInfo metadata '$key' is expected to be a string or null. Got: %s"
            );
        } catch (\Throwable $e) {
            throw new UnexpectedMetadataException($e->getMessage());
        }
        if (isset($this->streamMetadata[$key])) {
            return (string) $this->streamMetadata[$key];
        }

        return null;
    }
}
