# soluble-mediatools  

[![PHP 7.1+](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![Coverage](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, querying, thumbnailing... wraps around ffmpeg and ffprobe. Relies on symfony/process
under the hood and generally likes PSR (psr-log, psr-container), php 7.1 strict mode and ensure that substitution is 
possible when you need to customize (The 'S' of SOLID). 
      
## Status

Not yet 1.0 but what is documented works pretty well ;) Travis runs unit and integration/functional tests to ensure 
a good experience. But this project is young and will ❤️ to meet new contributors !

## Documentation 

All is here: **[https://soluble-io.github.io/soluble-mediatools/](https://soluble-io.github.io/soluble-mediatools/)**

## A quick taste

> Check the [doc](https://soluble-io.github.io/soluble-mediatools/) to get a better idea !!!

```php
<?php

use Soluble\MediaTools\Video\Config\{FFProbeConfig, FFMpegConfig};
use Soluble\MediaTools\Video\Exception\{InfoExceptionInterface, ConversionExceptionInterface};
use Soluble\MediaTools\Video\{InfoService, ThumbService, ThumbParams, ConversionService, ConversionParams};
use Soluble\MediaTools\Video\Filter;

$file = '/path/video.mp4';

// QUERYING

$infoService = new InfoService(new FFProbeConfig('/path/to/ffprobe'));

try {
    $videoInfo = $infoService->getInfo($file);
} catch (InfoExceptionInterface $e) {
    // see the chapter about exceptions
}

echo $videoInfo->getWidth();

// CONVERSION

$conversionService = new ConversionService(new FFMpegConfig('/path/to/ffmpeg'));

try {
    $conversionService->convert(
        $file, 
        '/path/output.mp4',
        (new ConversionParams())
             ->withVideoCodec('libx264')    
             ->withStreamable(true)
             ->withCrf(24)
             ->withVideoFilter(
                 new Filter\Hqdn3DVideoFilter()
             )
            
    );
} catch (ConversionExceptionInterface $e) {
    // see the chapter about exceptions
}

// THUMBNAILING

$thumbService = new ThumbService(new FFMpegConfig('/path/to/ffmpeg'));

try {
    $thumbService->makeThumbnail(
            $file, 
            '/path/outputFile.jpg', 
            (new ThumbParams())
                 ->withTime(1.123)
                 ->withQualityScale(5)
                 ->withVideoFilter(
                     new Filter\NlmeansVideoFilter()
                 )
        );
} catch (ConversionExceptionInterface $e) {
    
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


