![Logo](./docs/assets/images/mediatools.png)  

[![PHP 7.1+](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![Coverage](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
![PHPStan](https://img.shields.io/badge/style-level%207-brightgreen.svg?style=flat-square&label=phpstan)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Flexible audio/video conversions and thumbnailing for hi*php*ies.
Wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html)
and exposes most of their features, like scaling, clipping, filters, transcoding, audio extraction 
and much more.   

To prevent limitations, the API rather focus on providing developer fine-tuned parameters 
than giving ready-made recipes. Transcoding and conversions generally
requires specific processing, [judge by yourself](https://soluble-io.github.io/soluble-mediatools/video-conversion-service/#notes).
To help starting, the documentation includes a lot of examples and snippets you
can easily try and tune later. Check also [alternatives](./README.md#alternatives) wrappers
for ffmpeg, they are good and sometimes offer more magic if you're looking for it.
    
On another side, it likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container, psr-simplecache), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize
*(SOLID friendly)*. 

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.
     
## Documentation 

All is here: **[https://soluble-io.github.io/soluble-mediatools/](https://soluble-io.github.io/soluble-mediatools/)**

## Requirements

- PHP 7.1+
- FFmpeg/FFProbe 3.4+, 4.0+.
 
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
use Soluble\MediaTools\Video\VideoInfo;

$infoReader = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));

// Step 1: Read a media file

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // not a valid video (see exception)
}

$duration = $info->getDuration(); // total duration
$format   = $info->getFormatName(); // container format: mkv, mp4

// Step 2: Media streams info (video, subtitle, audio, data).

// Example with first video stream (streams are iterable)

try {    
    $video   = $info->getVideoStreams()->getFirst();
} catch (\Soluble\MediaTools\Video\Exception\NoStreamException $e) {
    // No video stream, 
}
    
$codec   = $video->getCodecName(); // i.e: vp9
$fps     = $video->getFps($decimals=0); // i.e: 24
$width   = $video->getWidth(); // i.e: 1080
$ratio   = $video->getAspectRatio();

// Alternate example  

if ($info->countStreams(VideoInfo::STREAM_TYPE_SUBTITLE) > 0) {
    $sub  = $info->getSubtitleStreams()->getFirst();
    $sub->getCodecName(); // webvtt
}

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


