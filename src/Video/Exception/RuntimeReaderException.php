<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

class RuntimeReaderException extends \Soluble\MediaTools\Common\Exception\RuntimeException implements ConverterExceptionInterface, AnalyzerExceptionInterface, InfoReaderExceptionInterface
{
}
