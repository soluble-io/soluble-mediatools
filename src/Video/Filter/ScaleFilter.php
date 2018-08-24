<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

class ScaleFilter implements FFMpegVideoFilterInterface
{
    public const OPTION_ASPECT_RATIO_INCREASE = 'increase';
    public const OPTION_ASPECT_RATIO_DECREASE = 'decrease';

    /**
     * Built-in aspect ratios.
     *
     * @var string[]
     */
    public const ASPECT_RATIO_MODES = [
        self::OPTION_ASPECT_RATIO_INCREASE,
        self::OPTION_ASPECT_RATIO_DECREASE,
    ];

    /** @var int|string */
    protected $height;

    /** @var int|string */
    protected $width;

    /** @var string|null */
    protected $aspect_ratio_mode;

    /**
     * Scale filter.
     *
     * @see https://trac.ffmpeg.org/wiki/Scaling
     *
     * @param string|int $width  as an int or any ffmpeg supported placeholder: iw*.5
     * @param string|int $height as an int or any ffmpeg supported placeholder: ih*2.3
     */
    public function __construct($width, $height, ?string $aspect_ratio_mode = null)
    {
        $this->width             = $width;
        $this->height            = $height;
        $this->aspect_ratio_mode = $aspect_ratio_mode;
    }

    public function getFFmpegCLIValue(): string
    {
        $scaleArg = sprintf(
            'scale=w=%s:h=%s',
            $this->width,
            $this->height
        );

        if ($this->aspect_ratio_mode !== null) {
            $scaleArg .= sprintf(
                ':force_original_aspect_ratio=%s',
                $this->aspect_ratio_mode
            );
        }

        return $scaleArg;
    }
}
