<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Assert;

use Soluble\MediaTools\Common\Exception\MissingBinaryException;

trait BinaryAssertionsTrait
{
    /**
     * Check for executable presence (skipped for executable in %PATH%).
     *
     * @throws MissingBinaryException
     */
    public function ensureIsExecutable(string $binaryFile): void
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
