<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Video\Adapter\FFMpegCLIValueInterface;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;

class ThumbParams implements ThumbParamsInterface
{
    /** @var array<string, bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface> */
    protected $params = [];

    /**
     * @param array<string, bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface> $params
     *
     * @throws InvalidArgumentException in case of unsupported option
     */
    public function __construct($params = [])
    {
        $this->ensureSupportedParams($params);
        $this->params = $params;
    }

    public function withSeekTime(SeekTime $seekTime): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_SEEK_TIME => $seekTime,
        ]));
    }

    public function withVideoFilter(VideoFilterInterface $videoFilter): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_VIDEO_FILTER => $videoFilter,
        ]));
    }

    /**
     * Set the underlying encoder quality scale. (-qscale:v <int>, alias to -q:v <int>).
     *
     * @param int $qualityScale a number interpreted by the encoder, generally 1-5
     */
    public function withQualityScale(int $qualityScale): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_QUALITY_SCALE => $qualityScale,
        ]));
    }

    /**
     * Add with overwrite option (default).
     *
     * @see self::withNoOverwrite()
     */
    public function withOverwrite(): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OVERWRITE => true
        ]));
    }

    /**
     * Add protection against output file overwriting.
     *
     * @see self::witoOverwrite()
     */
    public function withNoOverwrite(): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OVERWRITE => false
        ]));
    }

    public function withOutputFormat(string $outputFormat): self
    {
        return new self(array_merge($this->params, [
            self::PARAM_OUTPUT_FORMAT => $outputFormat,
        ]));
    }

    /**
     * Set a built-in param...
     *
     * @param bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface $paramValue
     *
     * @throws InvalidArgumentException in case of unsupported builtin param
     *
     * For static analysis the trick is to return 'self' instead of interface
     *
     * @return self
     */
    public function withBuiltInParam(string $paramName, $paramValue): ThumbParamsInterface
    {
        return new self(array_merge($this->params, [
            $paramName => $paramValue,
        ]));
    }

    /**
     * Return the internal array holding params.
     *
     * @return array<string,bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface>
     */
    public function toArray(): array
    {
        return $this->params;
    }

    public function isParamValid(string $paramName): bool
    {
        return in_array($paramName, self::BUILTIN_PARAMS, true);
    }

    /**
     * @param bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface|null $defaultValue if param does not exists set this one
     *
     * @return bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface|null
     */
    public function getParam(string $paramName, $defaultValue = null)
    {
        return $this->params[$paramName] ?? $defaultValue;
    }

    public function hasParam(string $paramName): bool
    {
        return array_key_exists($paramName, $this->params);
    }

    /**
     * Ensure that all params are supported.
     *
     * @param array<string, bool|string|int|VideoFilterInterface|FFMpegCLIValueInterface> $params
     *
     * @throws InvalidArgumentException in case of unsupported option
     */
    protected function ensureSupportedParams(array $params): void
    {
        foreach ($params as $paramName => $paramValue) {
            if (!$this->isParamValid($paramName)) {
                throw new InvalidArgumentException(
                    sprintf('Unsupported param "%s" given.', $paramName)
                );
            }
        }
    }
}
