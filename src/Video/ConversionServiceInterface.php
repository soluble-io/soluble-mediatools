<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Exception\ProcessConversionException;
use Soluble\MediaTools\VideoConversionParams;
use Symfony\Component\Process\Process;

interface ConversionServiceInterface
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
    public function getConversionProcess(string $inputFile, string $outputFile, VideoConversionParams $convertParams): Process;

    /**
     * Run a conversion, throw exception on error.
     *
     * @param callable|null                 $callback A PHP callback to run whenever there is some
     *                                                tmp available on STDOUT or STDERR
     * @param array<string,string|int>|null $env      An array of env vars to set
     *                                                when running the process
     *
     * @throws FileNotFoundException      When inputFile does not exists
     * @throws ProcessConversionException When the ffmpeg process conversion failed
     */
    public function convert(string $inputFile, string $outputFile, VideoConversionParams $convertParams, ?callable $callback = null, ?array $env = null): void;
}
