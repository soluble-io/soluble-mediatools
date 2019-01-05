<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Process\ProcessParams;

class FFProbeConfig implements FFProbeConfigInterface
{
    public const DEFAULT_TIMEOUT      = null;
    public const DEFAULT_IDLE_TIMEOUT = null;
    public const DEFAULT_ENV          = [];

    /** @var string */
    protected $binary;

    /** @var ProcessParamsInterface */
    protected $processParams;

    /**
     * @param string                    $ffprobeBinary FFProbeBinary, if null: 'ffprobe' on linux, 'ffmprobe.exe' on windows
     * @param float|null                $timeout       max allowed time (in seconds) for conversion, null for no timeout
     * @param float|null                $idleTimeout   max allowed idle time (in seconds) for conversion, null for no timeout
     * @param array<string, string|int> $env           An array of additional env vars to set when running the ffprobe process
     */
    public function __construct(
        ?string $ffprobeBinary = null,
        ?float $timeout = self::DEFAULT_TIMEOUT,
        ?float $idleTimeout = self::DEFAULT_IDLE_TIMEOUT,
        array $env = self::DEFAULT_ENV
    ) {
        $this->binary = $ffprobeBinary ?? self::getPlatformDefaultBinary();

        $this->processParams = new ProcessParams(
            $timeout,
            $idleTimeout,
            $env
        );
    }

    public static function getPlatformDefaultBinary(): string
    {
        return DIRECTORY_SEPARATOR === '\\' ? 'ffprobe.exe' : 'ffprobe';
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
