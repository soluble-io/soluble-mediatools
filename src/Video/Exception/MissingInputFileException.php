<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Exception\FileNotFoundException;

class MissingInputFileException extends FileNotFoundException implements ConversionExceptionInterface, DetectionExceptionInterface, InfoExceptionInterface
{
}
