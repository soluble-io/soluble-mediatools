<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Exception;

use Soluble\MediaTools\Common\Exception\FileNotFoundException;

class MissingInputFileException extends FileNotFoundException implements ConverterExceptionInterface, AnalyzerExceptionInterface, InfoReaderExceptionInterface
{
}
