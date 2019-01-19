<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

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
     * @return array<int|string, string>
     */
    public function getCliCommand(array $arguments, string $inputFile, $outputFile = null, array $prependArguments = []): array;

    public function getDefaultThreads(): ?int;
}
