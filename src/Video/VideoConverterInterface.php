<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video;

use Soluble\MediaTools\Common\IO\UnescapedFileInterface;
use Soluble\MediaTools\Common\Process\ProcessParamsInterface;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Symfony\Component\Process\Process;

interface VideoConverterInterface
{
    /**
     * Return ready-to-run symfony process object that you can use
     * to `run()` or `start()` programmatically. Useful if you want to make
     * things your way...
     *
     * @param null|string|UnescapedFileInterface $outputFile
     *
     * @throws InvalidArgumentException
     *
     * @see https://symfony.com/doc/current/components/process.html
     */
    public function getSymfonyProcess(string $inputFile, $outputFile, VideoConvertParamsInterface $convertParams, ?ProcessParamsInterface $processParams = null): Process;

    /**
     * Run a conversion, throw exception on error.
     *
     * @param null|string|UnescapedFileInterface $outputFile
     * @param callable|null                      $callback   A PHP callback to run whenever there is some
     *                                                       tmp available on STDOUT or STDERR
     *
     * @throws ConverterExceptionInterface When inputFile does not exists
     */
    public function convert(string $inputFile, $outputFile, VideoConvertParamsInterface $convertParams, ?callable $callback = null, ?ProcessParamsInterface $processParams = null): void;
}
