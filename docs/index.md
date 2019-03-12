[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Flexible audio/video conversions and thumbnailing for hi*php*ies.
Wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html)
and exposes most of their features, like scaling, clipping, filters, transcoding, audio extraction 
and much more.    

![Logo](./assets/images/mediatools.png)
 
To prevent limitations, the API rather focus on providing developer fine-tuned parameters 
than giving ready-made recipes. Transcoding and conversions generally
requires specific processing, [judge by yourself](./video-conversion-service/#notes).
To help starting, the documentation includes a lot of examples and snippets you
can easily try and tune later. Check also [alternatives](./#alternatives) wrappers
for ffmpeg, they are good and sometimes offer more magic if you're looking for it. 
     
It likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize 
*(SOLID friendly)*.
Last but not least, all services accepts a `LoggerInterface` one more reason to
prefer it from a simple command-line.

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.      

## Requirements

A PHP version >= 7.1 and depending on required services: ffmpeg and/or ffprobe.

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
        
        try {
            $videoInfo = $infoReader->getInfo('/path/video.mp4');
        } catch (InfoReaderExceptionInterface $e) {
            // Break here
        }
        
        // Total duration
        $duration = $videoInfo->getDuration();        
        // ffprobe format: i.e 'mov,mp4,m4a,3gp,3g2,mj2'
        $format   = $videoInfo->getFormatName();
        
        // Iterable video streams
        $videoStreams = $videoInfo->getVideoStreams();
                
        echo $videoStreams->getFirst()->getCodecName();
        
        $audioStreams = $videoInfo->AudioStreams();
        
        // ...
                
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


