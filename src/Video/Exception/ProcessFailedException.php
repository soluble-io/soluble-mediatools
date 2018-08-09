<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Common\Exception\ProcessException;

class ProcessFailedException extends ProcessException implements ConverterProcessExceptionInterface, AnalyzerProcessExceptionInterface, InfoProcessReaderExceptionInterface
{
}
