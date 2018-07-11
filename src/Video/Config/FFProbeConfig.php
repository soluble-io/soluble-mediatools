<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\ProcessParams;

class FFProbeConfig implements FFProbeConfigInterface
{
    public const DEFAULT_BINARY       = 'ffmpeg';
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    /** @var string */
    protected $binary;

    /** @var ProcessParamsInterface */
    protected $processParams;

    /**
     * @param string                    $ffprobeBinary FFProbeBinary, by default ffprobe
     * @param float|null                $timeout       max allowed time (in seconds) for conversion, null for no timeout
     * @param float|null                $idleTimeout   max allowed idle time (in seconds) for conversion, null for no timeout
     * @param array<string, string|int> $env           An array of additional env vars to set when running the ffprobe process
     */
    public function __construct(
        string $ffprobeBinary = self::DEFAULT_BINARY,
        ?float $timeout = self::DEFAULT_TIMEOUT,
        ?float $idleTimeout = self::DEFAULT_IDLE_TIMEOUT,
        array $env = self::DEFAULT_ENV
    ) {
        $this->binary        = $ffprobeBinary;
        $this->processParams = new ProcessParams(
            $timeout,
            $idleTimeout,
            $env
        );
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getProcessParams(): ProcessParamsInterface
    {
        return $this->processParams;
    }
}
