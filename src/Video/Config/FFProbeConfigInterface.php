<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Common\Process\ProcessParamsInterface;

interface FFProbeConfigInterface
{
    public function getBinary(): string;

    public function getProcessParams(): ProcessParamsInterface;
}
