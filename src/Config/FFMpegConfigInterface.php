<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Config;

interface FFMpegConfigInterface
{

    public function getBinary(): string;

    public function getThreads(): ?int;

    public function getTimeout(): ?int;

    public function getIdleTimeout(): ?int;

    public function getEnv(): array;
}
