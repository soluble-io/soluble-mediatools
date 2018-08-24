<?php

declare(strict_types=1);

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
