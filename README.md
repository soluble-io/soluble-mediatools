# soluble-mediatools  

[![PHP 7.1+](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![Coverage](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, querying, thumbnailing... wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html). 

It likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize 
*(SOLID friendly)*.   

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.

      
## Status

Not yet 1.0 but what is documented works pretty well ;) Travis runs unit and integration/functional 
tests to ensure a smooth experience. But **this project is young** and would ❤️ to meet new contributors !

## Roadmap

It's an attempt to make a swiss-army knife for medias managment in PHP, 
need to implement more services: image optimization, subtitle conversions... polish the API... 
 

## Documentation 

All is here: **[https://soluble-io.github.io/soluble-mediatools/](https://soluble-io.github.io/soluble-mediatools/)**

## A quick taste

> Check the [doc](https://soluble-io.github.io/soluble-mediatools/) to get a more detailed overview !!!

```php
<?php

use Soluble\MediaTools\Video\Config\{FFProbeConfig, FFMpegConfig};
use Soluble\MediaTools\Video\Exception\{InfoReaderExceptionInterface, ConverterExceptionInterface};
use Soluble\MediaTools\Video\{VideoInfoReader, VideoThumbGenerator, VideoThumbParams, VideoConverter, VideoConvertParams};
use Soluble\MediaTools\Video\Filter;

$file = '/path/video.mp4';

// QUERYING

$infoService = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));

try {
    $videoInfo = $infoService->getInfo($file);
} catch (InfoReaderExceptionInterface $e) {
    // see the chapter about exceptions
}

echo $videoInfo->getWidth();

// CONVERSION

$conversionService = new VideoConverter(new FFMpegConfig('/path/to/ffmpeg'));

try {
    $conversionService->convert(
        $file, 
        '/path/output.mp4',
        (new VideoConvertParams())
             ->withVideoCodec('libx264')    
             ->withStreamable(true)
             ->withCrf(24)
             ->withVideoFilter(
                 new Filter\Hqdn3DVideoFilter()
             )
            
    );
} catch (ConverterExceptionInterface $e) {
    // see the chapter about exceptions
}

// THUMBNAILING


$thumbService = new VideoThumbGenerator(new FFMpegConfig('/path/to/ffmpeg'));


try {
    $thumbService->makeThumbnail(
            $file, 
            '/path/outputFile.jpg', 
            (new VideoThumbParams())
                 ->withTime(1.123)
                 ->withQualityScale(5)
                 ->withVideoFilter(
                     new Filter\NlmeansVideoFilter()
                 )
        );
} catch (ConverterExceptionInterface $e) {
    
}

```

## Requirements

- PHP 7.1+
- FFmpeg/FFProbe 3.4+, 4.0+.

> For linux, you can easily download ffmpeg/ffprobe statically compiled binaries [here](https://johnvansickle.com/ffmpeg/), 
> alternatively have a look to the [travis install script](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh) too.
 
   
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)


