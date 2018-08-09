<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Assert;

use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\FileNotReadableException;

trait PathAssertionsTrait
{
    /**
     * @throws FileNotFoundException
     */
    protected function ensureFileExists(string $file): void
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exists', $file));
        }
    }

    /**
     * @throws FileNotReadableException
     * @throws FileNotFoundException
     */
    protected function ensureFileReadable(string $file): void
    {
        $this->ensureFileExists($file);
        if (!is_readable($file)) {
            throw new FileNotReadableException(sprintf(
                'File "%s" is not readable',
                $file
            ));
        }
    }
}
