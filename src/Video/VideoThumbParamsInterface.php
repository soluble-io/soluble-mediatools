<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Service\ActionParamInterface;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;

interface VideoThumbParamsInterface extends ActionParamInterface
{
    public const PARAM_QUALITY_SCALE       = 'QUALITY_SCALE';
    public const PARAM_VIDEO_FILTER        = 'VIDEO_FILTER';
    public const PARAM_SEEK_TIME           = 'SEEK_TIME';
    public const PARAM_OVERWRITE           = 'OVERWRITE';
    public const PARAM_OUTPUT_FORMAT       = 'OUTPUT_FORMAT';

    /**
     * Built-in params.
     *
     * @var string[]
     */
    public const BUILTIN_PARAMS = [
        self::PARAM_QUALITY_SCALE,
        self::PARAM_VIDEO_FILTER,
        self::PARAM_SEEK_TIME,
        self::PARAM_OVERWRITE,
        self::PARAM_OUTPUT_FORMAT,
    ];

    /**
     * Set a built-in param...
     *
     * @param string $paramName  a param that must exist in builtInParams
     * @param mixed  $paramValue
     *
     * @throws InvalidArgumentException in case of unsupported builtin param
     */
    public function withBuiltInParam(string $paramName, $paramValue): self;

    /**
     * Return ConversionParams without this one.
     */
    public function withoutParam(string $paramName): self;
}
