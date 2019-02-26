<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Info;

interface StreamTypeInterface
{
    public const AUDIO    = 'audio';
    public const VIDEO    = 'video';
    public const DATA     = 'data';
    public const SUBTITLE = 'subtitle';
}
