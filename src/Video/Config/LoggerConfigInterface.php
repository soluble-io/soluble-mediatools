<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Psr\Log\LoggerInterface;

interface LoggerConfigInterface
{
    public function getLogger(): LoggerInterface;
}
