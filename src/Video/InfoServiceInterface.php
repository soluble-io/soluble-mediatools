<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;


interface InfoServiceInterface
{
    public function getInfo(string $file): Info;
}
