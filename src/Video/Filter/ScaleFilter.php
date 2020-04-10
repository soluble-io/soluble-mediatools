<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Exception\ParamValidationException;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

final class ScaleFilter implements FFMpegVideoFilterInterface
{
    public const OPTION_ASPECT_RATIO_INCREASE = 'increase';
    public const OPTION_ASPECT_RATIO_DECREASE = 'decrease';
    public const OPTION_ASPECT_RATIO_DISABLE  = 'disable';

    /**
     * Built-in aspect ratios.
     */
    public const ASPECT_RATIO_MODES = [
        self::OPTION_ASPECT_RATIO_INCREASE,
        self::OPTION_ASPECT_RATIO_DECREASE,
        self::OPTION_ASPECT_RATIO_DISABLE,
    ];

    /** @var int|string|null */
    private $height;

    /** @var int|string|null */
    private $width;

    /** @var string|null */
    private $forceOriginalAspectRatio;

    /** @var string|null */
    private $eval;

    /** @var int|null */
    private $interl;

    /** @var string|null */
    private $flags;

    /** @var float|null */
    private $param0;

    /** @var float|null */
    private $param1;

    /** @var string|null */
    private $size;

    /** @var string|null */
    private $inColorMatrix;

    /** @var string|null */
    private $outColorMatrix;

    /** @var string|null */
    private $inRange;

    /** @var string|null */
    private $outRange;

    /**
     * Scale filter.
     *
     * @see https://trac.ffmpeg.org/wiki/Scaling
     * @see https://ffmpeg.org/ffmpeg-filters.html#scale-1
     *
     * @param string|int|null $width                    Set the output video width expression. Default value is the input width.
     * @param string|int|null $height                   Set the output video height expression. Default value is the input height.
     * @param string|null     $forceOriginalAspectRatio enable decreasing or increasing output video width or height if necessary to keep the original aspect ratio
     * @param string|null     $eval                     Specify when to evaluate width and height expression: 'init' or 'frame'
     * @param int|null        $interl                   Set the interlacing mode. It accepts the following values: ‘1’ Force interlaced aware scaling. ‘0’ Do not apply interlaced scaling. ‘-1’ Select interlaced aware scaling depending on whether the source frames are flagged as interlaced or not.
     * @param string|null     $flags                    Set libswscale scaling flags
     * @param float|null      $param0                   Set libswscale input parameters for scaling algorithms that need them
     * @param float|null      $param1                   Set libswscale input parameters for scaling algorithms that need them
     * @param string|null     $size                     set the video size
     * @param string|null     $inColorMatrix            set input YCbCr color space type
     * @param string|null     $outColorMatrix           set output YCbCr color space type
     * @param string|null     $inRange                  set intput YCbCr sample range
     * @param string|null     $outRange                 set output YCbCr sample range
     */
    public function __construct(
        $width = null,
        $height = null,
        ?string $forceOriginalAspectRatio = null,
        ?string $eval = null,
        ?int $interl = null,
        ?string $flags = null,
        ?float $param0 = null,
        ?float $param1 = null,
        ?string $size = null,
        ?string $inColorMatrix = null,
        ?string $outColorMatrix = null,
        ?string $inRange = null,
        ?string $outRange = null
    ) {
        if ($forceOriginalAspectRatio !== null &&
           !in_array($forceOriginalAspectRatio, self::ASPECT_RATIO_MODES, true)) {
            throw new ParamValidationException(sprintf(
                'Unsupported forceOriginalAspectRatio param: \'%s\'. Must be %s.',
                $forceOriginalAspectRatio,
                implode(' | ', self::ASPECT_RATIO_MODES)
            ));
        }
        $this->forceOriginalAspectRatio = $forceOriginalAspectRatio;

        $this->width          = $width;
        $this->height         = $height;
        $this->eval           = $eval;
        $this->interl         = $interl;
        $this->flags          = $flags;
        $this->param0         = $param0;
        $this->param1         = $param1;
        $this->size           = $size;
        $this->inColorMatrix  = $inColorMatrix;
        $this->outColorMatrix = $outColorMatrix;
        $this->inRange        = $inRange;
        $this->outRange       = $outRange;
    }

    public function getFFmpegCLIValue(): string
    {
        $args = array_filter([
            ($this->width !== null) ? "w={$this->width}" : false,
            ($this->height !== null) ? "h={$this->height}" : false,
            ($this->forceOriginalAspectRatio !== null) ? "force_original_aspect_ratio={$this->forceOriginalAspectRatio}" : false,
            ($this->eval !== null) ? "eval={$this->eval}" : false,
            ($this->interl !== null) ? "interl={$this->interl}" : false,
            ($this->flags !== null) ? "flags={$this->flags}" : false,
            ($this->param0 !== null) ? "param0={$this->param0}" : false,
            ($this->param1 !== null) ? "param1={$this->param1}" : false,
            ($this->size !== null) ? "size={$this->size}" : false,
            ($this->inColorMatrix !== null) ? "in_color_matrix={$this->inColorMatrix}" : false,
            ($this->outColorMatrix !== null) ? "out_color_matrix={$this->outColorMatrix}" : false,
            ($this->inRange !== null) ? "in_range={$this->inRange}" : false,
            ($this->outRange !== null) ? "out_range={$this->outRange}" : false,
        ]);

        return sprintf(
            'scale=%s',
            implode(':', $args)
        );
    }
}
