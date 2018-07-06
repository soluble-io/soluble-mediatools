<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\VideoConvertParams;
use Symfony\Component\Process\Process;

interface VideoConverterServiceInterface
{
    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want to make
     * things async...
     *
     * @see https://symfony.com/doc/current/components/process.html
     *
     * @throws FileNotFoundException when inputFile does not exists
     */
    public function getConversionProcess(string $inputFile, string $outputFile, VideoConvertParams $convertParams): Process;

    public function convert(string $inputFile, string $outputFile, VideoConvertParams $convertParams, ?callable $callback = null, ?array $env = null): void;
}
