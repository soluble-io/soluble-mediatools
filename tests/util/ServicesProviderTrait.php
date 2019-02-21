<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Util;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Video\Config\ConfigProvider;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\Config\FFProbeConfigInterface;
use Soluble\MediaTools\Video\VideoAnalyzerInterface;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\VideoInfoReaderInterface;
use Soluble\MediaTools\Video\VideoThumbGeneratorInterface;
use Zend\ServiceManager\ServiceManager;

trait ServicesProviderTrait
{
    /**
     * @throws \Exception
     */
    public function getVideoConvertService(): VideoConverterInterface
    {
        return $this->getConfiguredContainer()->get(VideoConverterInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getVideoInfoService(): VideoInfoReaderInterface
    {
        return $this->getConfiguredContainer()->get(VideoInfoReaderInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getVideoDetectionService(): VideoAnalyzerInterface
    {
        return $this->getConfiguredContainer()->get(VideoAnalyzerInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getVideoThumbService(): VideoThumbGeneratorInterface
    {
        return $this->getConfiguredContainer()->get(VideoThumbGeneratorInterface::class);
    }

    public function getFFMpegConfig(?string $ffmpegBinary = null): FFMpegConfigInterface
    {
        return $this->getConfiguredContainer(false, $ffmpegBinary)->get(FFMpegConfigInterface::class);
    }

    public function getFFProbeConfig(?string $ffprobeBinary = null): FFProbeConfigInterface
    {
        return $this->getConfiguredContainer(false, null, $ffprobeBinary)->get(FFProbeConfigInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getConfiguredContainer(bool $ensureBinariesExists = false, ?string $ffmpegBinary = null, ?string $ffprobeBinary = null): ContainerInterface
    {
        if (!defined('FFMPEG_BINARY_PATH')) {
            throw new \Exception('Missing phpunit constant FFMPEG_BINARY_PATH');
        }
        if (!defined('FFPROBE_BINARY_PATH')) {
            throw new \Exception('Missing phpunit constant FFPROBE_BINARY_PATH');
        }

        $ffmpegBinary  = $ffmpegBinary ?? FFMPEG_BINARY_PATH;
        $ffprobeBinary = $ffprobeBinary ?? FFPROBE_BINARY_PATH;

        if ($ensureBinariesExists) {
            if (mb_strpos($ffmpegBinary, './') !== false) {
                // relative directory
                $ffmpegBinary = realpath(FFMPEG_BINARY_PATH);
                if ($ffmpegBinary === false || !file_exists($ffmpegBinary)) {
                    throw new \Exception(sprintf(
                        'FFMPEG binary does not exists: \'%s\', realpath returned: \'%s\'',
                        FFMPEG_BINARY_PATH,
                        is_bool($ffmpegBinary) ? 'false' : $ffmpegBinary
                    ));
                }
            }

            if (mb_strpos($ffprobeBinary, './') !== false) {
                // relative directory
                $ffprobeBinary = realpath(FFPROBE_BINARY_PATH);
                if ($ffprobeBinary === false || !file_exists($ffprobeBinary)) {
                    throw new \Exception(sprintf(
                        'FFPROBE binary does not exists: \'%s\', realpath returned: \'%s\'',
                        FFPROBE_BINARY_PATH,
                        is_bool($ffprobeBinary) ? 'false' : $ffprobeBinary
                    ));
                }
            }
        }

        $config = [
            'soluble-mediatools' => [
                'ffmpeg.binary'  => $ffmpegBinary,
                'ffprobe.binary' => $ffprobeBinary,
            ],
        ];

        $sm = new ServiceManager(
            array_merge(
                [
                    'services' => [
                        'config' => $config,
                    ]],
                (new ConfigProvider())->getDependencies()
            )
        );

        return $sm;
    }
}
