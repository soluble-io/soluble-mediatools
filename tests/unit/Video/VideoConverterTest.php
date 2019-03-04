<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Video;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Common\IO\UnescapedFileInterface;
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\InvalidArgumentException;
use Soluble\MediaTools\Video\Filter\EmptyVideoFilter;
use Soluble\MediaTools\Video\Filter\Hqdn3DVideoFilter;
use Soluble\MediaTools\Video\Filter\NlmeansVideoFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\Filter\YadifVideoFilter;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Video\VideoConverter;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\VideoConvertParams;

class VideoConverterTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var VideoConverterInterface */
    protected $converter;

    public function setUp(): void
    {
        $this->converter = $this->getVideoConvertService();
    }

    public function testGetSymfonyProcessMustReturnCorrectParams(): void
    {
        $videoFilterChain = new VideoFilterChain();
        $videoFilterChain->addFilter(new EmptyVideoFilter());
        $videoFilterChain->addFilter(new YadifVideoFilter());
        $videoFilterChain->addFilter(new Hqdn3DVideoFilter());
        $videoFilterChain->addFilter(new NlmeansVideoFilter());

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('libvpx-vp9')
            ->withCrf(32)
            ->withVideoBitrate('200k')
            ->withVideoMaxBitrate('250000')
            ->withVideoMinBitrate('150k')
            ->withAudioCodec('libopus')
            ->withAudioBitrate('96k')
            ->withVideoFilter($videoFilterChain)
            ->withThreads(12)
            ->withSpeed(8)
            ->withKeyframeSpacing(240)
            ->withTileColumns(1)
            ->withFrameParallel(1)
            ->withPixFmt('yuv420p')
            ->withSeekStart(new SeekTime(1))
            ->withVideoFrames(200)
            ->withOutputFormat('webm');

        $process = $this->converter->getSymfonyProcess(
            __FILE__,
            '/path/output',
            $convertParams
        );

        // We test on unescaped command argument (because it's more convenient)
        $cmdLine = str_replace("'", '', $process->getCommandLine());

        self::assertStringContainsString(' -c:v libvpx-vp9 ', $cmdLine);
        self::assertStringContainsString(' -b:v 200k ', $cmdLine);
        self::assertStringContainsString(' -maxrate 250000', $cmdLine);
        self::assertStringContainsString(' -minrate 150k ', $cmdLine);
        self::assertStringContainsString(' -c:a libopus ', $cmdLine);
        self::assertStringContainsString(' -b:a 96k ', $cmdLine);
        self::assertStringContainsString(' -filter:v yadif=mode=0:parity=-1:deint=0,hqdn3d,nlmeans ', $cmdLine);
        self::assertStringContainsString(' -threads 12 ', $cmdLine);
        self::assertStringContainsString(' -speed 8 ', $cmdLine);
        self::assertStringContainsString(' -g 240 ', $cmdLine);
        self::assertStringContainsString(' -tile-columns 1 ', $cmdLine);
        self::assertStringContainsString(' -frame-parallel 1', $cmdLine);
        self::assertStringContainsString(' -pix_fmt yuv420p ', $cmdLine);
        self::assertStringContainsString(' -f webm ', $cmdLine);
        self::assertStringContainsString(' -ss 0:00:01.0 ', $cmdLine);
        self::assertStringContainsString(' -frames:v 200 ', $cmdLine);
        self::assertStringContainsString('/path/output', $cmdLine);
    }

    public function testGetSymfonyProcessMustThrowExceptionOnWrongOutput(): void
    {
        self::expectException(InvalidArgumentException::class);
        $convertParams = (new VideoConvertParams());

        (new VideoConverter(
            new FFMpegConfig('ffmpeg')
        ))->getSymfonyProcess(
            __FILE__,
            ['invalid array'],
            $convertParams
        );
    }

    public function testGetSymfonyProcessWithUnescapedFile(): void
    {
        $convertParams = (new VideoConvertParams());

        $process = (new VideoConverter(
            new FFMpegConfig('ffmpeg')
        ))->getSymfonyProcess(
            __FILE__,
            new class() implements UnescapedFileInterface {
                public function getFile(): string
                {
                    return '/a n/un \'escaped/file';
                }
            },
            $convertParams
        );

        // We test on unescaped command argument (because it's more convenient)
        $cmdLine = $process->getCommandLine();

        self::assertStringContainsString('/a n/un \'\\\'\'escaped/file\'', $cmdLine);
    }

    public function testGetSymfonyProcessMustDefaultToConfigThreads(): void
    {
        $convertParams = (new VideoConvertParams());

        $process = (new VideoConverter(
            new FFMpegConfig('ffmpeg', 3)
        ))->getSymfonyProcess(
            __FILE__,
            '/path/output',
            $convertParams
        );

        // We test on unescaped command argument (because it's more convenient)
        $cmdLine = str_replace("'", '', $process->getCommandLine());

        self::assertStringContainsString(' -threads 3 ', $cmdLine);

        // If null threads nothing must be set in cli

        $process = (new VideoConverter(
            new FFMpegConfig('ffmpeg', null)
        ))->getSymfonyProcess(
            __FILE__,
            '/path/output',
            $convertParams
        );

        $cmdLine = $process->getCommandLine();

        self::assertStringNotContainsStringIgnoringCase(' -threads ', $cmdLine);
    }
}
