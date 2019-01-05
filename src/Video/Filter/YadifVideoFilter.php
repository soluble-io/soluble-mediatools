<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoDeinterlacerInterface;

class YadifVideoFilter implements FFMpegVideoFilterInterface, VideoDeinterlacerInterface
{
    public const DEFAULT_MODE   = 0;
    public const DEFAULT_PARITY = -1;
    public const DEFAULT_DEINT  = 0;

    /** @var array<string, int> */
    protected $defaultOptions = [
        'mode'   => self::DEFAULT_MODE,
        'parity' => self::DEFAULT_PARITY,
        'deint'  => self::DEFAULT_DEINT,
    ];

    /** @var array<string, int> */
    protected $options = [];

    /**
     * @param int $mode   The interlacing mode to adopt (0, send_frame Output one frame for each frame)
     * @param int $parity default=-1 Enable automatic detection of field parity. 0:
     * @param int $deint  Specify which frames to deinterlace (0: all - Deinterlace all frames.)
     */
    public function __construct(int $mode = self::DEFAULT_MODE, int $parity = self::DEFAULT_PARITY, int $deint = self::DEFAULT_DEINT)
    {
        $this->options = array_merge($this->defaultOptions, [
            'mode'   => $mode,
            'parity' => $parity,
            'deint'  => $deint,
        ]);
    }

    public function getFFmpegCLIValue(): string
    {
        $yadifArg = sprintf(
            'yadif=mode=%s:parity=%s:deint=%s',
            $this->options['mode'],
            $this->options['parity'],
            $this->options['deint']
        );

        return $yadifArg;
    }
}
