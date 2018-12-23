[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Flexible video conversions and thumbnailing for hi*php*ies.
Wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html)
and exposes most of their features, like scaling, clipping, filters, transcoding 
and much more.   
 
![Logo](./assets/images/mediatools.png)

Its API rather focus on giving developer freedom and tunability than ready-made recipes. 
Conversions are [far from being straightforward](./video-conversion-service/#notes),
so if you're looking for more ready-made cookies, check the [alternatives](./#alternatives), 
they work well too. *I still believe mediatools dance better though ;)*  
    
On another side, it likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize 
*(SOLID friendly)*.  
Last but not least, all services accepts a `LoggerInterface` one more reason to
prefer it from a simple command-line.

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.      

## Status

Not yet 1.0 but what's documented works pretty well ;) Travis runs unit and integration/functional 
tests to ensure a smooth experience. But **this project is young** and would ❤️ to meet new contributors !

## Requirements

A PHP version >= 7.1 and depending on required services: ffmpeg and ffprobe.

## Features at a glance

### Video services

- [x] **VideoConverter** ([doc here](./video-conversion-service.md))
      - [x] Conversions: transcode, compress, transmux...
      - [x] Clipping (start-time/send-time)
      - [x] Filters: scale, deinterlace, denoise... and [others](./video-filters.md).        
        ```php hl_lines="4 5 6 7 8 9 10 12 13 14 15 16"
        <?php // A quick taste            
        $converter = new VideoConverter(new FFMpegConfig('/path/to/ffmpeg'));
        
        $params = (new VideoConvertParams())
            ->withVideoCodec('libx264')    
            ->withStreamable(true)
            ->withVideoFilter(
                new ScaleFilter(720, 576)
            )
            ->withCrf(24);                  
            
        $converter->convert(
            '/path/inputFile.mov', 
            '/path/outputFile.mp4', 
            $params
        );           
        ```  
      
- [x] **VideoInfoReader** ([doc here](./video-info-service.md))
      - [x] Duration, dimensions, number of frames
        ```php hl_lines="4 6 7 8 9 10"
        <?php // a quick taste    
        $infoReader = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));
        
        $videoInfo = $infoReader->getInfo('/path/video.mp4');
        
        $duration = $videoInfo->getDuration();
        $frames   = $videoInfo->getNbFrames();
        $width    = $videoInfo->getWidth();
        $height   = $videoInfo->getHeight();
        ```  

- [x] **VideoThumbGenerator** ([doc here](./video-thumb-service.md))
      - [x] Thumbnail at specific time or frame.
      - [x] Filters: scale, deinterlace, denoise... and [others](./video-filters.md). 
        ```php hl_lines="4 5 6 7 8 10 11 12 13 14 15"
        <?php // a quick taste        
        $generator = new VideoThumbGenerator(new FFMpegConfig('/path/to/ffmpeg'));
        
        $params = (new VideoThumbParams())
            ->withVideoFilter(
                new ScaleFilter(720, 576)
            )        
            ->withTime(1.25);
            
        $generator->makeThumbnail(
            '/path/inputFile.mov', 
            '/path/outputFile.jpg', 
            $params
        );    
        ```  

- [x] **VideoAnalyzer** ([doc here](./video-detection-service.md))
      - [x] Interlacing detection
        ```php hl_lines="4 6 7 8 9 10"
        <?php // a quick taste        
        $analyzer = new VideoAnalyzer(new FFMpegConfig('/path/to/ffmpeg'));
        
        $interlaceGuess = $analyzer->detectInterlacement();
                    
        $interlaced = $interlaceGuess->isInterlaced();
        ```  
     

## Alternatives

- [https://github.com/PHP-FFMpeg/PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [https://github.com/char0n/ffmpeg-php](https://github.com/char0n/ffmpeg-php) 


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



