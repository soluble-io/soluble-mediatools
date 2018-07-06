# soluble-mediatools  

[![PHP Version](http://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Media tools: toolbox for media processing, video conversions, transcoding, transmuxing, thumbnailing... wraps around ffmpeg and ffprobe. 

## Status  

> Early stages... VideoConverter service almost API stable, other will follow soon.

## Features

> Mediatools services:

- [X] `Video\ConverterService`.
  - [X] Transcoding, transmuxing, compression (audio/video)
  - [X] Video Filters
      - [X] Chainable filters
      - [X] Deinterlacing video (Yadif, Hqdn3d)  
  - [ ] Video scaling (todo)
  - [ ] Time slicing (todo)        
  - [ ] Option to enable multipass transcoding (todo)
- [X] `Video\ProbeService` 
  - [X] Basic information like duration, frames....
  - [ ] Need API Stabilization    
- [X] `Video\ThumbService`
  - [X] Basic thumbnail creation
  - [ ] Stabilize API first
- [X] `Video\DetectionService`.
  - [X] Interlacement detection (thru probablility).  


## Requirements

- PHP 7.1+
- FFmpeg 3.4+, 4.0+ 
 
-------------- 
## Video\ConverterService. 
--------------

The `Video\ConvertServiceInterface` offers two ways to convert a video to another one.

<TODO>

### Exception

### Recipes

> The following examples assumes that the `Video\ConvertServiceInterface`  
> is already configured *(generally the services will be available through
> a psr-11 compatible container or through framework integration... 
> See [configuration](#configuration) section for more info)*      
>
> ```php
> <?php
> use Psr\Container\ContainerInterface;
> use Soluble\MediaTools\Video\ConverterServiceInterface;
> /**
>  * @var ContainerInterface        $aPsr11Container 
>  * @var ConverterServiceInterface $videoConverter
>  */ 
> $videoConverter = $aPsr11Container->get(ConverterServiceInterface::class);
> ```

#### Conversion from `mov` to `mp4/x264/aac`

> See the [official H264](https://trac.ffmpeg.org/wiki/Encode/H.264) doc. 

```php
<?php
use Soluble\MediaTools\Video\ConvertParams;
use Soluble\MediaTools\Exception as MTException;

$convertParams = (new ConvertParams)
            ->withVideoCodec('libx264')
            ->withAudioCodec('aac')
            ->withAudioBitrate('128k')            
            ->withStreamable(true)      // Add streamable options (movflags & faststart) 
            ->withCrf(24)               // Level of compression: better size / less visual quality  
            ->withPreset('fast')        // Optional: see presets  
            ->withOutputFormat('mp4');  // Optional: if not set, will be detected from output file extension.
    
try {
    /** @var \Soluble\MediaTools\Video\ConverterServiceInterface $videoConverter */
    $videoConverter->convert('/path/inputFile.mov', '/path/outputFile.mp4', $convertParams);    
} catch(MTException\ProcessConversionException $e) {
    
    // The ffmpeg 'symfony' process encountered a failure...
    // To see the reason you can either use:
    echo $e->getMessage();                      // full message 
    echo $e->getProcess()->getErrorOutput();    // process stdErr
    echo $e->wasCausedByTimeout() ? 'timeout' : '';
    echo $e->wasCausedBySignal() ? 'interrupted' : '';
    
} catch (MTException\FileNotFoundException $e) {
     echo "The input file does not exists";    
}
       
``` 

#### Conversion from `mov` to `webm/vp9/opus`

> See the official [ffmpeg VP9 docs](https://trac.ffmpeg.org/wiki/Encode/VP9) 
> and have a look at the [google vp9 VOD](https://developers.google.com/media/vp9/settings/vod/#ffmpeg_command_lines) guidelines


```php
<?php
use Soluble\MediaTools\Video\ConvertParams;
use Soluble\MediaTools\Exception as MTException;


$convertParams = (new ConvertParams())
                ->withVideoCodec('libvpx-vp9')
                ->withVideoBitrate('750k')
                ->withQuality('good')
                ->withCrf(33)
                ->withAudioCodec('libopus')
                ->withAudioBitrate('128k')
                /**
                 * It is recommended to allow up to 240 frames of video between keyframes (8 seconds for 30fps content).
                 * Keyframes are video frames which are self-sufficient; they don't rely upon any other frames to render
                 * but they tend to be larger than other frame types.
                 * For web and mobile playback, generous spacing between keyframes allows the encoder to choose the best
                 * placement of keyframes to maximize quality.
                 */
                ->withKeyframeSpacing(240)
                // Most of the current VP9 decoders use tile-based, multi-threaded decoding.
                // In order for the decoders to take advantage of multiple cores,
                // the encoder must set tile-columns and frame-parallel.
                ->withTileColumns(2)
                ->withFrameParallel(1)
                ->withSpeed(1)
                // Optional: Use videoprobe to be sure of color conversions if any needed
                // ->withPixFmt('yuv420p') 
                ->withOutputFormat('webm');


try {
    /** @var \Soluble\MediaTools\Video\ConverterServiceInterface $videoConverter */
    $videoConverter->convert('/path/inputFile.mov', '/path/outputFile.webm', $convertParams);
} catch(MTException\ProcessConversionException $e) {
    
    // The ffmpeg 'symfony' process encountered a failure...
    // To see the reason you can either use:
    echo $e->getMessage();                      // full message 
    echo $e->getProcess()->getErrorOutput();    // process stdErr
    echo $e->wasCausedByTimeout() ? 'timeout' : '';
    echo $e->wasCausedBySignal() ? 'interrupted' : '';
    
} catch (MTException\FileNotFoundException $e) {
     echo "The input file does not exists";    
}

``` 

----------------------
## Video\DetectionService service. 
----------------------

### Recipes

> The following examples assumes that the `Video\DetectionServiceInterface`  
> is already configured *(generally the services will be available through
> a psr-11 compatible container or through framework integration... 
> See [configuration](#configuration) section for more info)*      
>
> ```php
> <?php
> use Psr\Container\ContainerInterface;
> use Soluble\MediaTools\Video\DetectionServiceInterface;
> /**
>  * @var ContainerInterface        $aPsr11Container 
>  * @var DetectionServiceInterface $detectService
>  */ 
> $detectService = $aPsr11Container->get(DetectionServiceInterface::class);
> ```


#### Detect interlacement

> In some cases, detect interlaced videos cannot be achieved trivially 
> through video metadata. When unsure we can use the `InterlaceGuess` 
> to decide whether the video is interlaced (BFF or TFF) and conditionnally
> add de-interlace filters (i.e. yadif)to conversion or thumbnail generation.
> 
> For more background see the [ffmpeg filters](https://ffmpeg.org/ffmpeg-filters.html) reference@see 
> and [https://askubuntu.com/a/867203](https://askubuntu.com/a/867203).

```php
<?php
use Soluble\MediaTools\Exception as MTException;
use Soluble\MediaTools\Video\Detection\{InterlaceDetect, InterlaceGuess};

$videoFile = '/path/input_video.webm';
$maxFramesToAnalyze = InterlaceDetect::DEFAULT_INTERLACE_MAX_FRAMES; // 1000

try {
    /** @var \Soluble\MediaTools\Video\DetectionServiceInterface $detectService */
    $interlaceGuess = $detectService->detectInterlacement($videoFile, $maxFramesToAnalyze);
} catch (MTException\FileNotFoundException $e) {
    // The input file does not exists
    throw $e;
} 

// Optional detection threshold:
// By default 0.2 = 20% interlaced frames => interlaced video
$threshold = InterlaceGuess::DEFAULT_DETECTION_THRESHOLD; 

$isInterlaced = $interlaceGuess->isInterlaced($threshold);

switch($interlaceGuess->getBestGuess($threshold)) {
    case InterlaceGuess::MODE_INTERLACED_TFF:        
    case InterlaceGuess::MODE_INTERLACED_BFF:
        $isInterlaced = true;
        break;
    case InterlaceGuess::MODE_PROGRESSIVE:
    case InterlaceGuess::MODE_UNDETERMINED:
    default:
        $isInterlaced = false;                    
}

// Or get the stats
$stats = $interlaceGuess->getStats();

// Then decide to apply a de-interlace filter to thumbnail generation
// or video conversion.

```

---------------------------
## Configuration
---------------------------

### Usage with psr/container

> MediaTools provides factories that can be easily used with any for psr-11 compatible container.
> See the [ConfigProvider](./src/Config/ConfigProvider.php) class to get an idea of how 
> service registration can be achieved.  

### With zend-service-manager.

```php
<?php declare(strict_types=1);

use Soluble\MediaTools\Config\ConfigProvider;
use Soluble\MediaTools\Video\ConverterServiceInterface;
use Soluble\MediaTools\Video\ProbeServiceInterface;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Zend\ServiceManager\ServiceManager;

// Config options can be in a file, i.e: `require 'config/soluble-mediatools.global.php';`
// or set via dotenv...

$config = [
    'soluble-mediatools' => [
        /**
         * Binaries
         */
        'ffmpeg.binary'         => 'ffmpeg',   // or a complete path /opt/local/ffmpeg/bin/ffmpeg
        'ffprobe.binary'        => 'ffprobe',  // or a complete path /opt/local/ffmpeg/bin/ffprobe

        /**
         * Conversion options
         */
        'ffmpeg.conversion.threads'      => null,   // <null>: single thread; <0>: number of cores, <1+>: number of threads
        'ffmpeg.conversion.timeout'      => null,   // <null>: no timeout, <number>: number of seconds before timing-out
        'ffmpeg.conversion.idle_timeout' => 60,     // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'ffmpeg.conversion.env'          => []      // An array of additional env vars to set when running the ffmpeg conversion process        
    ],    
];

// Service manager
$container = new ServiceManager(
                array_merge([
                    // In Zend\ServiceManager configuration will be set
                    // in 'services'.'config'. 
                    'services' => [
                        'config' => $config
                    ]],
                    // Here the factories
                    (new ConfigProvider())->getDependencies()
             ));

// Now whenever you want an instance of a service:

$videoConverter   = $container->get(ConverterServiceInterface::class);
$videoProbe       = $container->get(ProbeServiceInterface::class);
$videoDetection   = $container->get(DetectionServiceInterface::class);

//$videoThumb     = $container->get(VideoThumb::class);

```
  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

