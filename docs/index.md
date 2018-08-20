[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, querying, thumbnailing... wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html). 

It likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize 
*(SOLID friendly)*.   

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.
      
## Status

Not yet 1.0 but what's documented works pretty well ;) Travis runs unit and integration/functional 
tests to ensure a smooth experience. But **this project is young** and would ❤️ to meet new contributors !

## Implemented services

### Video\VideoConverter

> Full doc: [here](./video-conversion-service.md)

```php hl_lines="8 9 10 11 14 15 16 17 18"
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
 
### VideoInfoReader 

> Full doc: [here](./video-info-service.md)

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

### VideoThumbGenerator 

> Full doc: [here](./video-thumb-service.md)

```php hl_lines="8 9 12 13 14 15 16"
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

### VideoAnalyzer

> Full doc: [here](./video-detection-service.md)

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

## Requirements

A PHP version >= 7.1 and depending on required services: ffmpeg and ffprobe.

> For linux, you can easily download statically compiled binaries [here](https://johnvansickle.com/ffmpeg/), 
> alternatively have a look to the [travis install script](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh) too.


## Installation

Installation in your project

```bash
$ composer require soluble/mediatools
``` 

## Project philosophy

???+ Info "Note for developers"
    
    - Mediatools is an [opensource](https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md) PHP 7.1+ library and :heart: pull requests and [contributors](https://github.com/soluble-io/soluble-mediatools/blob/master/CONTRIBUTING.md).     
    
    - Mediatools will preferably use PSR standards. It currently allows injection of 
      any PSR-3 compatible logger and provides integrations for PSR-11 container interface.
      (PSR-7 for video thumbnailing is under consideration).     
    
    - Mediatools likes chainability when possible, but completely forbid the use of fluent interfaces in favour
      of immutable api (like PSR-7).  
    
    - Customization can generally be done easily as the project try to respect subsitution 
      principles as much as possible. In most cases, you can swap implementations as you like.
      
    - Mediatools versions adheres to [semantic versioning](http://semver.org/). 
      No bc-break outside of major version releases and we keep a [changelog](https://github.com/soluble-io/soluble-mediatools/blob/master/CHANGELOG.md).  
      
    - Quality assurance is guaranteed through unit and integration/functional tests, 
      [phpstan](https://github.com/phpstan/phpstan) and some php-cs sniffs. 
      Travis is used as continuous integration server.  
        
    - Mediatools is currently released as a monolithic repository, but once more
      diverse services exists (image optimisation, resizing, subtitles conversion...) the architecture
      can be easily split into multiple repositories without affecting existing projects.
      
    - Note that information and thumbnail services can be used in realtime but conversions should be 
      used with a job queue. Conversions are heavy.    


## Alternative(s)

- https://github.com/PHP-FFMpeg/PHP-FFMpeg


