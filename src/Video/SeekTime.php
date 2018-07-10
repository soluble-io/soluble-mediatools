<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Converter\FFMpegCLIValueInterface;

class SeekTime implements FFMpegCLIValueInterface
{
    /** @var float */
    protected $time;

    /**
     * @param float $seconds seconds and optional milliseconds as
     */
    public function __construct(float $seconds)
    {
        $this->time = $seconds;
    }

    /**
     * Convert 'HOURS:MM:SS.MILLISECONDS' format to seconds with milli
     * Note: FFMpeg refer to this format as 'sexagesimal'.
     *
     * @param string $hmsmTime 'HOURS:MM:SS.MILLISECONDS' like in '01:23:45.678'
     *
     * @return float i.e 123.642
     *
     * @throws InvalidArgumentException
     */
    public static function convertHMSmToSeconds(string $hmsmTime): float
    {
        [   $secondsWithMilli,
            $minutes,
            $hours,
        ] = array_merge(array_reverse(explode(':', $hmsmTime)), [0, 0, 0]);

        if (!is_numeric($secondsWithMilli) || $secondsWithMilli < 0 || $secondsWithMilli >= 61.0) {
            throw new InvalidArgumentException(sprintf(
                'Seconds \'%s\' are incorrect in \'%s\'',
                $secondsWithMilli,
                $hmsmTime
            ));
        }

        if (!is_numeric($minutes) || $minutes < 0 || $minutes > 60.0) {
            throw new InvalidArgumentException(sprintf(
                'Minutes \'%s\' are incorrect in \'%s\'',
                $minutes,
                $hmsmTime
            ));
        }

        if (!is_numeric($hours) || $hours < 0) {
            throw new InvalidArgumentException(sprintf(
                'Hours \'%s\' are incorrect in \'%s\'',
                $hours,
                $hmsmTime
            ));
        }

        return (float) $secondsWithMilli + ((int) $minutes) * 60 + ((int) $hours) * 3600;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function convertSecondsToHMSs(float $secondsWithMilli): string
    {
        if ($secondsWithMilli < 0) {
            throw new InvalidArgumentException(sprintf(
                "Cannot convert negative time to HMSs: \'%s\'",
                (string) $secondsWithMilli
            ));
        }

        [$time, $milli] = array_merge(explode('.', (string) $secondsWithMilli), [0, 0]);

        return sprintf(
            '%d:%02d:%02d.%d',
            ((int) $time / 3600),
            ((int) $time % 3600 / 60),
            ((int) $time % 3600 % 60),
            $milli
        );
    }

    /**
     * Return time in seconds with milli.
     *
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @param string $hmsmTime 'HOURS:MM:SS.MILLISECONDS' like in '01:23:45.678'
     *
     * @throws InvalidArgumentException
     */
    public static function createFromHMS(string $hmsmTime): self
    {
        return new self(self::convertHMSmToSeconds($hmsmTime));
    }

    public function getFFmpegCLIValue(): string
    {
        return self::convertSecondsToHMSs($this->time);
    }
}
