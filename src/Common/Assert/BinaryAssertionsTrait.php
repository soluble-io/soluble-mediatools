<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Common\Assert;

use Soluble\MediaTools\Common\Exception\MissingBinaryException;

trait BinaryAssertionsTrait
{
    /**
     * Check for executable presence (skipped for executable in %PATH%).
     *
     * @throws MissingBinaryException
     */
    protected function ensureBinaryAvailable(string $binaryFile): void
    {
        // Case of binary (no path given), we cannot tell
        if (basename($binaryFile) === $binaryFile) {
            $exists     = true; // assume it exists
            $executable = true;
        } else {
            $exists     = file_exists($binaryFile);
            $executable = is_executable($binaryFile);
        }

        if (!$exists || !$executable) {
            throw new MissingBinaryException(sprintf(
                'Missing \'%s\' binary: (exists: %s, executable: %s)',
                $binaryFile,
                $exists ? 'true' : 'false',
                $executable ? 'true' : 'false'
            ));
        }
    }
}
