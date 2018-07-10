<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;
use Soluble\MediaTools\Video\Filter\Type\VideoFilterInterface;

class VideoFilterChain implements FFMpegVideoFilterInterface
{
    /** @var VideoFilterInterface[] */
    protected $filters = [];

    public function __construct()
    {
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addFilter(VideoFilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * @throws UnsupportedParamValueException
     */
    public function getFFmpegCLIValue(): string
    {
        $values = [];
        foreach ($this->filters as $filter) {
            if (!$filter instanceof FFMpegVideoFilterInterface) {
                throw new UnsupportedParamValueException(
                    sprintf(
                        'Filter \'%s\' have not been made compatible with FFMpeg',
                        get_class($filter)
                    )
                );
            }
            $val = $filter->getFFmpegCLIValue();
            if ($val === '') {
                continue;
            }

            $values[] = $val;
        }
        if (count($values) === 0) {
            return '';
        }

        return implode(',', $values);
    }
}
