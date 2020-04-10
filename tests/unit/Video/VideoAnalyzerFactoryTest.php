<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2020 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Soluble\MediaTools\Video\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Video\VideoAnalyzerFactory;

class VideoAnalyzerFactoryTest extends TestCase
{
    use ServicesProviderTrait;

    public function setUp(): void
    {
    }

    public function testWithTestLogger(): void
    {
        $logger = new NullLogger();

        $ffmpegConfig = $this->prophesize(FFMpegConfigInterface::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has(\Soluble\MediaTools\Video\Logger\LoggerInterface::class)->willReturn(true);
        $container->get(\Soluble\MediaTools\Video\Logger\LoggerInterface::class)->willReturn($logger);
        $container->get(FFMpegConfigInterface::class)->willReturn($ffmpegConfig->reveal());

        $videoConverter = new VideoAnalyzerFactory();
        $videoConverter->__invoke($container->reveal());
        self::assertTrue(true);
    }
}
