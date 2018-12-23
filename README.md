![Logo](./docs/assets/images/mediatools.png)  

[![PHP 7.1+](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![Coverage](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Flexible video conversions and thumbnailing for hi*php*ies.
Wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html)
and exposes most of their features, like scaling, clipping, filters, transcoding 
and much more.   

To prevent limitations, the API rather focus on providing developer fine-tuned parameters 
than giving ready-made recipes. Transcoding and conversions generally
requires specific processing, [judge by yourself](https://soluble-io.github.io/soluble-mediatools/video-conversion-service/#notes).
To help starting, the documentation includes a lot of examples and snippets you
can easily try and tune later. Check also [alternatives](./README.md#alternatives) wrappers
for ffmpeg, they are good and sometimes offer more magic if you're looking for it.
    
On another side, it likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize
*(SOLID friendly)*. Last but not least, all services accepts a `LoggerInterface` one more reason to
prefer it from a simple command-line.  

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.
     
## Status

Not yet 1.0 but what is documented works pretty well ;) Travis runs unit and integration/functional 
tests to ensure a smooth experience. But **this project is young** and would ❤️ to meet new contributors !

## Documentation 

All is here: **[https://soluble-io.github.io/soluble-mediatools/](https://soluble-io.github.io/soluble-mediatools/)**

## Requirements

- PHP 7.1+
- FFmpeg/FFProbe 3.4+, 4.0+.

## Roadmap

It's an attempt to make a swiss-army knife for medias managment in PHP, 
need to implement more services: image optimization, subtitle conversions... polish the API... 
 
## Features

> Check the [doc](https://soluble-io.github.io/soluble-mediatools/) to get a more detailed overview !!!

### Implemented services

#### VideoConverter

> Full doc: [here](https://soluble-io.github.io/soluble-mediatools/video-conversion-service/)

```php
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\{VideoConverter, VideoConvertParams};

$converter = new VideoConverter(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new VideoConvertParams())
    ->withVideoCodec('libx264')    
    ->withStreamable(true)
    ->withCrf(24);                  
    
try {    
    $converter->convert(
        '/path/inputFile.mov', 
        '/path/outputFile.mp4', 
        $params
    );    
} catch(ConverterExceptionInterface $e) {
    // See chapter about exception !!!    
}
       
```  
 
#### VideoInfoReader 

> Full doc: [here](https://soluble-io.github.io/soluble-mediatools/video-info-service/)

```php
<?php
use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\VideoInfoReader;

$infoReader = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));

try {
    $videoInfo = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // see below for exceptions
}

$duration = $videoInfo->getDuration();
$frames   = $videoInfo->getNbFrames();
$width    = $videoInfo->getWidth();
$height   = $videoInfo->getHeight();

// Or alternatively
['width' => $width, 'height' => $height] = $videoInfo->getDimensions();
       
``` 

#### VideoThumbGenerator 

> Full doc: [here](https://soluble-io.github.io/soluble-mediatools/video-thumb-service/)

```php
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\{VideoThumbGenerator, VideoThumbParams, SeekTime};

$generator = new VideoThumbGenerator(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new VideoThumbParams())
    ->withTime(1.25);
    
try {    
    $generator->makeThumbnail(
        '/path/inputFile.mov', 
        '/path/outputFile.jpg', 
        $params
    );    
} catch(ConverterExceptionInterface $e) {
    // See chapter about exception !!!    
}
       
``` 

#### VideoAnalyzer

> Full doc: [here](https://soluble-io.github.io/soluble-mediatools/video-detection-service/)

```php
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\AnalyzerExceptionInterface;
use Soluble\MediaTools\Video\VideoAnalyzer;

$analyzer = new VideoAnalyzer(new FFMpegConfig('/path/to/ffmpeg'));

    
try {    
    $interlaceGuess = $analyzer->detectInterlacement(
        '/path/input.mov',
        // Optional:
        //   $maxFramesToAnalyze, default: 1000
        $maxFramesToAnalyze = 200
    );
    
} catch(AnalyzerExceptionInterface $e) {
    // See chapter about exception !!!    
}

$interlaced = $interlaceGuess->isInterlaced(
    // Optional: 
    //  $threshold, default 0.25 (if >=25% interlaced frames, then true) 
    0.25
);

``` 

## Alternatives

- [https://github.com/PHP-FFMpeg/PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [https://github.com/char0n/ffmpeg-php](https://github.com/char0n/ffmpeg-php) 
   
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)


