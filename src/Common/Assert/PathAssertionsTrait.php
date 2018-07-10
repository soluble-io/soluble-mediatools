<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Assert;

use Soluble\MediaTools\Exception\FileNotFoundException;

trait PathAssertionsTrait
{
    /**
     * @throws FileNotFoundException
     */
    protected function ensureFileExists(string $file): void
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exists or is not readable', $file));
        }
    }
}
