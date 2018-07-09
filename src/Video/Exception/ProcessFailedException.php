<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Exception\ProcessException;

class ProcessFailedException extends ProcessException implements ConversionProcessExceptionInterface, DetectionProcessExceptionInterface, InfoProcessExceptionInterface
{
}
