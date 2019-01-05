<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Adapter;

use Soluble\MediaTools\Common\Exception\UnsupportedParamException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Common\IO\UnescapedFileInterface;
use Soluble\MediaTools\Video\VideoConvertParamsInterface;

interface ConverterAdapterInterface
{
    /**
     * @return array<string, string>
     *
     * @throws UnsupportedParamException
     * @throws UnsupportedParamValueException
     */
    public function getMappedConversionParams(VideoConvertParamsInterface $conversionParams): array;

    /**
     * @param array<string,string>               $arguments        args that will be added
     * @param null|string|UnescapedFileInterface $outputFile
     * @param array<string,string>               $prependArguments args that must be added at the beginning of the command
     *
     * @return array<int, string>
     */
    public function getCliCommand(array $arguments, string $inputFile, $outputFile = null, array $prependArguments = []): array;

    public function getDefaultThreads(): ?int;
}
