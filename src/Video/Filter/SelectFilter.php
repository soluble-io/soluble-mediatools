<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

class SelectFilter implements FFMpegVideoFilterInterface
{
    /** @var null|string */
    private $expression;

    /**
     * Select filter.
     *
     * @see https://ffmpeg.org/ffmpeg-filters.html#select_002c-aselect
     *
     * @param string|null $expression ffmpeg selected expression
     */
    public function __construct(
        ?string $expression = null
    ) {
        $this->expression = $expression;
    }

    public function getFFmpegCLIValue(): string
    {
        return sprintf(
            'select=%s',
            str_replace('"', '\"', $this->expression ?? '')
        );
    }
}
