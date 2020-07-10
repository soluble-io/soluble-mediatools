<?php

declare(strict_types=1);

namespace MediaToolsTest\Util;

/**
 * Basic polyfills for phpunit 7.4/8 and 9.
 */
trait PhpUnitPolyfillsTrait
{
    public function expectExceptionMessageMatchesPolyfilled(string $regexp): void
    {
        if (is_callable(['parent', 'expectExceptionMessageMatches'])) {
            $this->expectExceptionMessageMatches($regexp);
        } else {
            $this->expectExceptionMessageRegExp($regexp);
        }
    }

    public static function assertMatchesRegularExpressionPolyfilled(string $regexp, string $string): void
    {
        if (is_callable(['parent', 'assertMacthesRegularExpression'])) {
            parent::assertMatchesRegularExpression($regexp, $string);
        } else {
            parent::assertRegExp($regexp, $string);
        }
    }

    public static function assertFileDoesNotExistPolyfilled(string $file): void
    {
        if (is_callable(['parent', 'assertFileDoesNotExists'])) {
            parent::assertFileDoesNotExists($file);
        } else {
            parent::assertFileNotExists($file);
        }
    }
}
