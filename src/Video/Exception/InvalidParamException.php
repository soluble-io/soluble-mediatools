<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Exception\RuntimeException as RTE;

class InvalidParamException extends RTE implements ConversionExceptionInterface, DetectionExceptionInterface, InfoExceptionInterface
{
}
