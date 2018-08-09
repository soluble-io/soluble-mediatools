<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Common\Exception\RuntimeException as RTE;

class UnsetParamReaderException extends RTE implements ConverterExceptionInterface, AnalyzerExceptionInterface, InfoReaderExceptionInterface
{
}
