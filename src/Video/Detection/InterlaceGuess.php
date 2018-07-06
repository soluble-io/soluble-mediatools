<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Detection;

use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterInterface;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;

class InterlaceGuess
{
    /**
     * Default interlacing detection threshold
     * 20% frames detected interlaced is a sufficient
     * threshold to detect interlacing.
     */
    public const INTERLACING_DETECTION_THRESHOLD = 0.2;

    public const MODE_INTERLACED_BFF = 'INTERLACED_BFF';
    public const MODE_INTERLACED_TFF = 'INTERLACED_TFF';
    public const MODE_PROGRESSIVE    = 'PROGRESSIVE';
    public const MODE_UNDETERMINED   = 'UNDETERMINED';

    /** @var float */
    protected $detection_threshold;

    /** @var int */
    protected $total_frames;

    /** @var array<string, int> */
    protected $detected_frames;

    /** @var array<string, float> */
    protected $percent_frames;

    /**
     * @param float $detection_threshold in percent: i.e 0.8, 0.6...
     */
    public function __construct(
        int $nb_frames_interlaced_tff,
        int $nb_frames_interlaced_bff,
        int $nb_frames_progressive,
        int $nb_frames_undetermined,
        float $detection_threshold = self::INTERLACING_DETECTION_THRESHOLD
    ) {
        $this->detection_threshold = $detection_threshold;
        $detected_frames           = [
            self::MODE_INTERLACED_TFF => $nb_frames_interlaced_tff,
            self::MODE_INTERLACED_BFF => $nb_frames_interlaced_bff,
            self::MODE_PROGRESSIVE    => $nb_frames_progressive,
            self::MODE_UNDETERMINED   => $nb_frames_undetermined,
        ];
        arsort($detected_frames, SORT_NUMERIC);
        $this->detected_frames = $detected_frames;
        $this->total_frames    = (int) array_sum(array_values($this->detected_frames));
        $this->percent_frames  = [];
        foreach ($this->detected_frames as $key => $value) {
            $this->percent_frames[$key] = $value / $this->total_frames;
        }
    }

    /**
     * @return float[]
     */
    public function getStats(): array
    {
        return $this->percent_frames;
    }

    public function getBestGuess(?float $threshold = null): string
    {
        $min_pct = $threshold !== null ? $threshold : $this->detection_threshold;
        reset($this->detected_frames);
        $bestGuessKey = (string) key($this->detected_frames);
        if ($this->percent_frames[$bestGuessKey] >= $min_pct) {
            return $bestGuessKey;
        }

        return self::MODE_UNDETERMINED;
    }

    /**
     * Whether the video seems to be interlaced in TFF (top field first)
     * within a certain probability threshold.
     *
     * @param float|null $threshold
     *
     * @return bool
     */
    public function isInterlacedTff(?float $threshold = null): bool
    {
        $min_pct = $threshold !== null ? $threshold : $this->detection_threshold;

        return $this->percent_frames[self::MODE_INTERLACED_TFF] >= $min_pct;
    }

    /**
     * Whether the video seems to be interlaced in BFF (bottom field first)
     * within a certain probability threshold.
     *
     * @param float|null $threshold
     *
     * @return bool
     */
    public function isInterlacedBff(?float $threshold = null): bool
    {
        $min_pct = $threshold !== null ? $threshold : $this->detection_threshold;

        return $this->percent_frames[self::MODE_INTERLACED_BFF] >= $min_pct;
    }

    /**
     * Whether the video seems to be interlaced either in BFF (bottom field first)
     * or TFF (top field first) within a certain probability threshold.
     *
     * @param float|null $threshold
     *
     * @return bool
     */
    public function isInterlaced(?float $threshold = null): bool
    {
        return $this->isInterlacedBff($threshold) || $this->isInterlacedTff($threshold);
    }

    public function isProgressive(?float $threshold = null): bool
    {
        $min_pct = $threshold !== null ? $threshold : $this->detection_threshold;

        return $this->percent_frames[self::MODE_PROGRESSIVE] >= $min_pct;
    }

    public function isUndetermined(?float $threshold = null): bool
    {
        $min_pct = $threshold !== null ? $threshold : $this->detection_threshold;

        return $this->percent_frames[self::MODE_UNDETERMINED] >= $min_pct;
    }

    /**
     * @see https://ffmpeg.org/ffmpeg-filters.html (section yadif)
     * @see https://askubuntu.com/a/867203
     *
     * @param float|null $threshold
     *
     * @return EmptyVideoFilter|YadifVideoFilter
     */
    public function getDeinterlaceVideoFilter(?float $threshold = null): VideoFilterInterface
    {
        if (!$this->isInterlaced($threshold)) {
            return new EmptyVideoFilter();
        }
        $parity = YadifVideoFilter::DEFAULT_PARITY;
        if ($this->isInterlacedBff($threshold)) {
            // parity=1,  bff - Assume the bottom field is first.
            $parity = 1;
        } elseif ($this->isInterlacedTff($threshold)) {
            // parity=0,  tff - Assume the top field is first.
            $parity = 0;
        }

        return new YadifVideoFilter(YadifVideoFilter::DEFAULT_MODE, $parity, YadifVideoFilter::DEFAULT_DEINT);
    }
}
