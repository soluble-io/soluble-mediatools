<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;

interface FFProbeConfigInterface extends ProcessParamsInterface
{
    public function getBinary(): string;
}
