<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Exception\RuntimeException;

class InvalidParamException extends RuntimeException implements ConversionExceptionInterface, DetectionExceptionInterface, InfoExceptionInterface
{
}
