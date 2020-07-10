<?php

namespace MediaToolsTest\Util;

/**
 * Basic polyfills for phpunit 7.4/8 and 9
 */
trait PhpUnitPolyfillTrait
{
    public function expectExceptionMessageMatchesPolyfilled(string $regexp): void
    {
        if (is_callable(['parent', 'expectExceptionMessageMatches'])) {
            $this->expectExceptionMessageMatches($regexp);
        } else {
            $this->expectExceptionMessageRegExp($regexp);
        }

    }

    static public function assertMatchesRegularExpressionPolyfilled(string $regexp, string $string): void
    {
        if (is_callable(['parent', 'assertMacthesRegularExpression'])) {
            parent::assertMatchesRegularExpression($regexp, $string);
        } else {
            parent::assertRegExp($regexp, $string);
        }
    }


    static public function assertFileDoesNotExistPolyfilled(string $file): void
    {
        if (is_callable(['parent', 'assertFileDoesNotExists'])) {
            parent::assertFileDoesNotExists($file);
        } else {
            parent::assertFileNotExists($file);
        }
    }
}
