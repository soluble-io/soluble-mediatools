<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Soluble\MediaTools\Common\Math\NumberConversion;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;

final class AspectRatio
{
    public const DEFAULT_PROPORTION_SEPARATOR = ':';

    /** @var float */
    private $x;

    /** @var float */
    private $y;

    /** @var string */
    private $separator;

    public function __construct(float $x, float $y, string $separator = self::DEFAULT_PROPORTION_SEPARATOR)
    {
        $this->x         = $x;
        $this->y         = $y;
        $this->separator = $separator;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @param string $proportions
     *
     * @throws InvalidArgumentException
     */
    public static function createFromString(string $proportions, string $separator = self::DEFAULT_PROPORTION_SEPARATOR): self
    {
        if (mb_substr_count($proportions, $separator) !== 1) {
            throw new InvalidArgumentException(sprintf(
                'Cannot parse given proportions: \'%s\' with separator \'%s\' (missing or multiple occurences)',
                $proportions,
                $separator
            ));
        }

        [$x, $y] = explode($separator, $proportions);

        if (!is_numeric($x) || !is_numeric($y)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot parse given proportions: \'%s\', x and y must be valid numerics',
                $proportions
            ));
        }

        return new self((float) $x, (float) $y);
    }

    public function getString(?string $separator = null, ?int $maxDecimals = null): string
    {
        return sprintf(
            '%s%s%s',
            $this->getFloatAsString($this->x, $maxDecimals),
            $separator ?? $this->separator,
            $this->getFloatAsString($this->y, $maxDecimals)
        );
    }

    public function __toString(): string
    {
        return $this->getString();
    }

    private function getFloatAsString(float $number, ?int $maxDecimals = null): string
    {
        $n = (string) $number;

        if ($n === (string) ((int) ($number))) {
            return $n;
        }

        if ($maxDecimals === null) {
            return $n;
        }

        return (string) NumberConversion::truncateFloat($number, $maxDecimals);
    }
}
