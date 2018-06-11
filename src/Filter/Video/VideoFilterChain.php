<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Filter\Video;

class VideoFilterChain extends AbstractVideoFilter
{
    /** @var VideoFilterInterface[] */
    protected $filters = [];

    public function __construct()
    {
    }

    public function getFilters($filter): array
    {
        return $this->filters;
    }

    public function addFilter(VideoFilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    public function getFFmpegCLIValue(): string
    {
        $values = [];
        foreach ($this->filters as $filter) {
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
