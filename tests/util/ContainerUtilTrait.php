<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Util;

use Psr\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

trait ContainerUtilTrait
{
    public function createZendServiceManager(?array $mediaToolsConfig, string $entryName = 'config'): ContainerInterface
    {
        return new ServiceManager(
            array_merge(
                [
                    'services' => [
                        $entryName => //(new ConfigProvider())->getDefaultConfiguration(),
                            $mediaToolsConfig,
                    ]],
                [] //could be: (new ConfigProvider)->getDependencies()
            )
        );
    }
}
