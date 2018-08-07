[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, querying, thumbnailing... wraps around ffmpeg and ffprobe. 

## Status

Not yet 1.0 but what is documented works pretty well ;)


???+ Info "Note for developers"
    
    - Mediatools is [opensource](https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md) PHP 7.1+ project and :heart: pull requests and [contributors](https://github.com/soluble-io/soluble-mediatools/blob/master/CONTRIBUTING.md).     
    
    - Mediatools will preferably use PSR standards. It currently allows injection of 
      any PSR-3 compatible logger and provides integrations for PSR-11 container interface.
      (PSR-7 for video thumbnailing is under consideration).     
    
    - Mediatools likes chainability when possible, but completely forbid the use of fluent interfaces in favour
      of immutable api (like PSR-7).  
    
    - Customization can generally be done easily as the project try to respect subsitution 
      principles as much as possible. In most cases, you can swap implementations as you like.
      
    - Mediatools versions adheres to [semantic versioning](http://semver.org/). 
      No bc-break outside of major version releases and we keep a [changelog](https://github.com/soluble-io/soluble-mediatools/blob/master/CHANGELOG.md).  
      
    - Quality assurance is guaranteed through unit and integration/functional tests, phpstan and
      and some php-cs sniffs. Travis is used as continuous integration server.  
        
    - The soluble-mediatools is currently released as a monolithic repository, but once more
      diverse services exists (image optimisation, resizing, subtitles conversion...) the architecture
      can be easily split into multiple repositories without affecting existing projects.
      
    - Information and thumbnail services can be used in realtime but conversions should be 
      used with a job queue.     

## Basic code example

> Look at the specific service documentation for more info.

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

A PHP version >= 7.1 and depending on required services: ffmpeg and ffprobe.

> For linux, you can easily download statically compiled binaries [here](https://johnvansickle.com/ffmpeg/), 
> alternatively have a look to the [travis install script](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh) too.


## Installation

Installation in your project

```bash
$ composer require soluble/mediatools
``` 


## Features

> Video services:

- [X] [Video\ConversionService](/video-conversion-service) for conversions, transcoding,
  video filters (deinterlace, denoise), audio conversions, video clipping...
- [X] [Video\InfoService](/video-info-service) to query video metadata (dimensions, frames...) 
- [X] [Video\ThumbService](/video-thumb-service) to make thumbnails of a video.
- [X] [Video\DetectionService](/video-detection-service ) analyze video stream and use inference to detected [interlacement](https://en.wikipedia.org/wiki/Interlaced_video) *(BFF, TFF)* or [progressive](https://en.wikipedia.org/wiki/Progressive_scan) enconding in videos. More to come.  

## Alternative(s)

- https://github.com/PHP-FFMpeg/PHP-FFMpeg

