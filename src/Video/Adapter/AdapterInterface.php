<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Adapter;

use Soluble\MediaTools\Common\Exception\UnsupportedParamException;
use Soluble\MediaTools\Common\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Video\ConversionParamsInterface;

interface AdapterInterface
{
    /**
     * @return array<string, string>
     *
     * @throws UnsupportedParamException
     * @throws UnsupportedParamValueException
     */
    public function getMappedConversionParams(ConversionParamsInterface $conversionParams): array;

    /**
     * @param array       $arguments
     * @param null|string $inputFile
     * @param null        $outputFile
     *
     * @return string
     */
    public function getCliCommand(array $arguments, ?string $inputFile, $outputFile = null): string;
}
