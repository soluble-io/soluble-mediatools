<?php

declare(strict_types=1);

namespace MediaToolsTest\Functional\UseCases;

use MediaToolsTest\Util\ServicesProviderTrait;
use PHPUnit\Framework\TestCase;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\Filter\Hqdn3DVideoFilter;
use Soluble\MediaTools\Video\Filter\ScaleFilter;
use Soluble\MediaTools\Video\Filter\VideoFilterChain;
use Soluble\MediaTools\Video\VideoConverterInterface;
use Soluble\MediaTools\Video\VideoConvertParams;

class ImageFilterTest extends TestCase
{
    use ServicesProviderTrait;

    /** @var VideoConverterInterface */
    protected $videoConvert;

    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $outputDir;

    /** @var string */
    protected $imgFile;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->videoConvert = $this->getVideoConvertService();

        $this->baseDir   = dirname(__FILE__, 3);
        $this->outputDir = "{$this->baseDir}/tmp";
        $this->imgFile   = "{$this->baseDir}/data/test.jpg";
    }

    public function testConversionOfImageWithFilters(): void
    {
        $outputFile = "{$this->outputDir}/testConversionOfImageWithFilters.jpg";

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $convertParams = (new VideoConvertParams())
            ->withVideoCodec('mjpeg')
            ->withVideoFilter(new VideoFilterChain(
                [
                    new ScaleFilter(320, 200),
                    new Hqdn3DVideoFilter()
                ]
            ))
            ->withVideoQualityScale(2);

        self::assertFileExists($this->imgFile);
        self::assertFileNotExists($outputFile);

        try {
            $this->videoConvert->convert($this->imgFile, $outputFile, $convertParams);
        } catch (ConverterExceptionInterface $e) {
            self::fail(sprintf('Failed to convert image: %s', $e->getMessage()));
        }

        self::assertFileExists($outputFile);
        unlink($outputFile);
    }
}
