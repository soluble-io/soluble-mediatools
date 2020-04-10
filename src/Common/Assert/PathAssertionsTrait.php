<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Assert;

use Soluble\MediaTools\Common\Exception\FileEmptyException;
use Soluble\MediaTools\Common\Exception\FileNotFoundException;
use Soluble\MediaTools\Common\Exception\FileNotReadableException;

trait PathAssertionsTrait
{
    /**
     * @param bool $ensureFileNotEmpty check also filesize to be greater than 0
     *
     * @throws FileNotFoundException
     * @throws FileEmptyException    if $ensureFileNotEmpty is true
     */
    private function ensureFileExists(string $file, bool $ensureFileNotEmpty = false): void
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exists', $file));
        }
        if (!is_file($file)) {
            throw new FileNotFoundException(sprintf(
                'File "%s" is not a regular file (%s)',
                $file,
                is_dir($file) ? 'directory' : 'unknow type'
            ));
        }

        if ($ensureFileNotEmpty && filesize($file) === 0) {
            throw new FileEmptyException(sprintf('File "%s" is empty', $file));
        }
    }

    /**
     * @param bool $ensureFileNotEmpty check also filesize to be greater than 0
     *
     * @throws FileNotReadableException
     * @throws FileNotFoundException
     * @throws FileEmptyException       if $ensureFileNotEmpty is true
     */
    private function ensureFileReadable(string $file, bool $ensureFileNotEmpty = false): void
    {
        $this->ensureFileExists($file, $ensureFileNotEmpty);
        if (!is_readable($file)) {
            throw new FileNotReadableException(sprintf(
                'File "%s" is not readable',
                $file
            ));
        }
    }
}
