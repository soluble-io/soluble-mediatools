<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

use Soluble\MediaTools\Video\Exception\NoStreamException;

interface SubtitleStreamCollectionInterface extends StreamCollectionInterface
{
    /**
     * @throws NoStreamException
     */
    public function getFirst(): SubtitleStreamInterface;
}
