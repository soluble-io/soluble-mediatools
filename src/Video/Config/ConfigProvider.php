<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Config;

use Soluble\MediaTools\Video\VideoAnalyzer;
use Soluble\MediaTools\Video\VideoAnalyzerFactory;
use Soluble\MediaTools\Video\VideoAnalyzerInterface;
use Soluble\MediaTools\Video\VideoConverter;
use Soluble\MediaTools\Video\VideoConverterFactory;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\VideoInfoReaderFactory;
use Soluble\MediaTools\Video\VideoInfoReaderInterface;
use Soluble\MediaTools\Video\VideoThumbGenerator;
use Soluble\MediaTools\Video\VideoThumbGeneratorFactory;
use Soluble\MediaTools\Video\VideoThumbGeneratorInterface;

class ConfigProvider
{
    /**
     * @return array<string, array<string,array<string,mixed>>>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array<string, array<string,string>>
     */
    public function getDependencies(): array
    {
        return [
            'aliases'   => $this->getAliases(),
            'factories' => $this->getFactories(),
        ];
    }

    /**
     * Return concrete implementation aliases if needed.
     *
     * @return array<string, string>
     */
    public function getAliases(): array
    {
        return [
            // Configuration holders
            FFMpegConfig::class  => FFMpegConfigInterface::class,
            FFProbeConfig::class => FFProbeConfigInterface::class,

            // Services
            VideoConverter::class      => VideoConverterInterface::class,
            VideoInfoReader::class     => VideoInfoReaderInterface::class,
            VideoAnalyzer::class       => VideoAnalyzerInterface::class,
            VideoThumbGenerator::class => VideoThumbGeneratorInterface::class,
        ];
    }

    /**
     * Return interface based factories.
     *
     * @return array<string, string>
     */
    public function getFactories(): array
    {
        return [
            // Configuration holders
            FFMpegConfigInterface::class  => FFMpegConfigFactory::class,
            FFProbeConfigInterface::class => FFProbeConfigFactory::class,

            // Services classes
            VideoConverterInterface::class      => VideoConverterFactory::class,
            VideoInfoReaderInterface::class     => VideoInfoReaderFactory::class,
            VideoAnalyzerInterface::class       => VideoAnalyzerFactory::class,
            VideoThumbGeneratorInterface::class => VideoThumbGeneratorFactory::class,

            // Logger
            //LoggerConfigInterface::class    => <Factory to create / too much choice>
        ];
    }

    /**
     * @return array<string, array<string,mixed>>
     */
    public static function getDefaultConfiguration(): array
    {
        $baseDir = dirname(__DIR__, 3);
        if (!is_dir($baseDir)) {
            throw new \RuntimeException(sprintf(
                'Cannot locate library directory: %s', $baseDir
            ));
        }
        $config = implode(DIRECTORY_SEPARATOR, [$baseDir, 'config', 'soluble-mediatools.config.php']);
        if (!is_file($config) || !is_readable($config)) {
            throw new \RuntimeException(sprintf('Missing project default configuration: %s', $config));
        }
        return require $config;
    }
}
