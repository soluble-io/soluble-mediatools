<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Converter;

use Soluble\MediaTools\Exception\UnsupportedParamException;
use Soluble\MediaTools\Exception\UnsupportedParamValueException;
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
}
