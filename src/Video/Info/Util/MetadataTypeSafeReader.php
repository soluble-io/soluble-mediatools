<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info\Util;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Exception\UnexpectedMetadataException;

class MetadataTypeSafeReader
{
    /** @var array<string, mixed> */
    private $streamMetadata;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param array<string, mixed> $streamMetadata
     */
    public function __construct(array $streamMetadata, ?LoggerInterface $logger = null)
    {
        $this->streamMetadata = $streamMetadata;
        $this->logger         = $logger ?? new NullLogger();
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyIntValue(string $key): int
    {
        $value = $this->streamMetadata[$key] ?? '<empty>';
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $msg = sprintf(
                "The ffprobe/videoInfo metadata '$key' is expected to be an integer. Got: %s (%s)",
                gettype($value),
                (string) $value
            );
            $this->logger->notice($msg);
            throw new UnexpectedMetadataException($msg);
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
        $value = $this->streamMetadata[$key] ?? null;
        if ($value !== null && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $msg = sprintf(
                "The ffprobe/videoInfo metadata '$key' is expected to be an integer or null. Got: %s (%s)",
                gettype($value),
                (string) $value
            );
            $this->logger->notice($msg);
            throw new UnexpectedMetadataException($msg);
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
        $value = $this->streamMetadata[$key] ?? '<empty>';
        if (!is_numeric($value)) {
            $msg = sprintf(
                "The ffprobe/videoInfo metadata '$key' is expected to be a float. Got: %s (%s)",
                gettype($value),
                (string) $value
            );
            $this->logger->notice($msg);

            throw new UnexpectedMetadataException($msg);
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
        $value = $this->streamMetadata[$key] ?? null;
        if ($value !== null && !is_numeric($value)) {
            $msg = sprintf(
                "The ffprobe/videoInfo metadata '$key' is expected to be a float or null. Got: %s (%s)",
                gettype($value),
                (string) $value
            );
            $this->logger->notice($msg);

            throw new UnexpectedMetadataException($msg);
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
        $value = $this->streamMetadata[$key] ?? null;
        if ($value !== null && !is_scalar($value)) {
            $msg = sprintf(
                "The ffprobe/videoInfo metadata '$key' is expected to be a string or null. Got: %s",
                gettype($value)
            );
            $this->logger->notice($msg);
            throw new UnexpectedMetadataException($msg);
        }

        if (isset($this->streamMetadata[$key])) {
            return (string) $this->streamMetadata[$key];
        }

        return null;
    }

    /**
     * @param string $key metadata key
     *
     * @throws UnexpectedMetadataException
     */
    public function getKeyStringValue(string $key): string
    {
        $value = $this->streamMetadata[$key] ?? null;
        if (!is_scalar($value)) {
            $msg = sprintf(
                "The ffprobe/videoInfo metadata '$key' is expected to be a string. Got: %s",
                gettype($value)
            );
            $this->logger->notice($msg);
            throw new UnexpectedMetadataException($msg);
        }

        return (string) $this->streamMetadata[$key];
    }
}
