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

- [X] `VideoConversionService`.
  - [X] Transcoding, transmuxing, compression (audio/video)
  - [X] Video Filters
      - [X] Chainable filters
      - [X] Deinterlacing video (Yadif, Hqdn3d)
  - [X] Time crop (seekstart - seekend)      
  - [ ] Video scaling (todo)          
  - [ ] Option to enable multipass transcoding (todo)
- [X] `VideoInfoService` 
  - [X] Basic information like duration, frames....
  - [ ] Need API Stabilization    
- [X] `VideoThumbService`
  - [X] Basic thumbnail creation
  - [ ] Stabilize API first
- [X] `VideoDetectionService`.
  - [X] Infer/detect [interlaced](https://en.wikipedia.org/wiki/Interlaced_video) *(BFF, TFF)* vs [progressive](https://en.wikipedia.org/wiki/Progressive_scan) encoded videos.  


## Requirements

- PHP 7.1+
- FFmpeg 3.4+, 4.0+ 
 
-------------- 
## VideoConversionService. 


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
> use Soluble\MediaTools\Video\ConversionServiceInterface;
> /**
>  * @var ContainerInterface        $aPsr11Container 
>  * @var ConversionServiceInterface $videoConverter
>  */ 
> $videoConverter = $aPsr11Container->get(ConversionServiceInterface::class);
> ```

#### Conversion from `mov` to `mp4/x264/aac`

> See the [official H264](https://trac.ffmpeg.org/wiki/Encode/H.264) doc. 

```php
<?php
use Soluble\MediaTools\VideoConversionParams;
use Soluble\MediaTools\Exception as MTException;

$convertParams = (new VideoConversionParams)
            ->withVideoCodec('libx264')
            ->withAudioCodec('aac')
            ->withAudioBitrate('128k')            
            ->withStreamable(true)      // Add streamable options (movflags & faststart) 
            ->withCrf(24)               // Level of compression: better size / less visual quality  
            ->withPreset('fast');       // Optional: see presets           
    
try {
    /** @var \Soluble\MediaTools\Video\ConversionServiceInterface $videoConverter */
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
use Soluble\MediaTools\VideoConversionParams;
use Soluble\MediaTools\Exception as MTException;


$convertParams = (new VideoConversionParams)
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
    /** @var \Soluble\MediaTools\Video\ConversionServiceInterface $videoConverter */
    $videoConverter->convert('/path/inputFile.mov', '/path/outputFile.webm', $convertParams);
} catch(MTException\ProcessConversionException $e) {
    // ...        
} catch (MTException\FileNotFoundException $e) {
     echo "The input file does not exists";    
}

``` 

#### Time crop

> See the official [ffmpeg VP9 docs](https://trac.ffmpeg.org/wiki/Encode/VP9) 
> and have a look at the [google vp9 VOD](https://developers.google.com/media/vp9/settings/vod/#ffmpeg_command_lines) guidelines


```php
<?php
use Soluble\MediaTools\VideoConversionParams;
use Soluble\MediaTools\Video\SeekTime;
use Soluble\MediaTools\Exception as MTException;

$convertParams = (new VideoConversionParams)
                ->withSeekStart(new SeekTime(10.242)) // 10 sec, 242 milli
                ->withSeekEnd(SeekTime::createFromHMS('12:52.015')); // 12 mins, 52 secs...                

try {
    /** @var \Soluble\MediaTools\Video\ConversionServiceInterface $videoConverter */
    $videoConverter->convert('/path/inputFile.mp4', '/path/outputFile.mp4', $convertParams);
} catch(MTException\ProcessConversionException $e) {
    // ...        
} catch (MTException\FileNotFoundException $e) {
     echo "The input file does not exists";    
}

``` 

----------------------
## VideoThumbService  

### Recipes

> The following examples assumes that the `Video\ThumbServiceInterface`  
> is already configured *(generally the services will be available through
> a psr-11 compatible container or through framework integration... 
> See [configuration](#configuration) section for more info)*      
>
> ```php
> <?php
> use Psr\Container\ContainerInterface;
> use Soluble\MediaTools\Video\ThumbServiceInterface;
> /**
>  * @var ContainerInterface     $aPsr11Container 
>  * @var ThumbServiceInterface  $thumbService
>  */ 
> $thumbService = $aPsr11Container->get(ThumbServiceInterface::class);
> ```


#### Create a thumbnail

```php
<?php
use Soluble\MediaTools\Exception as MTException;
use Soluble\MediaTools\Video\SeekTime;

$videoFile = '/path/input_video.webm';

try {
    /** @var \Soluble\MediaTools\Video\ThumbServiceInterface $thumbService */
    $thumbService->makeThumbnail($videoFile, '/path/thumb.jpg', new SeekTime(4.25));
} catch (MTException\FileNotFoundException $e) {
    // The input file does not exists
    throw $e;
} 

```

----------------------
## VideoDetectionService. 


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
> through video metadata. When unsure we can use the `InterlaceDetectGuess` 
> to decide whether the video is interlaced (BFF or TFF) and conditionnally
> add de-interlace filters (i.e. yadif)to conversion or thumbnail generation.
> 
> For more background see the [ffmpeg filters](https://ffmpeg.org/ffmpeg-filters.html) reference@see 
> and [https://askubuntu.com/a/867203](https://askubuntu.com/a/867203).

```php
<?php
use Soluble\MediaTools\Exception as MTException;
use Soluble\MediaTools\Video\Detection\{InterlaceDetect, InterlaceDetectGuess};

$videoFile = '/path/input_video.webm';
$maxFramesToAnalyze = InterlaceDetect::DEFAULT_INTERLACE_MAX_FRAMES; // 1000

try {
    /** @var \Soluble\MediaTools\Video\DetectionServiceInterface $detectService */
    $InterlaceDetectGuess = $detectService->detectInterlacement($videoFile, $maxFramesToAnalyze);
} catch (MTException\FileNotFoundException $e) {
    // The input file does not exists
    throw $e;
} 

// Optional detection threshold:
// By default 0.2 = 20% interlaced frames => interlaced video
$threshold = InterlaceDetectGuess::DEFAULT_DETECTION_THRESHOLD; 

$isInterlaced = $InterlaceDetectGuess->isInterlaced($threshold);

switch($InterlaceDetectGuess->getBestGuess($threshold)) {
    case InterlaceDetectGuess::MODE_INTERLACED_TFF:        
    case InterlaceDetectGuess::MODE_INTERLACED_BFF:
        $isInterlaced = true;
        break;
    case InterlaceDetectGuess::MODE_PROGRESSIVE:
        $isInterlaced = false;
        break;        
    case InterlaceDetectGuess::MODE_UNDETERMINED:
        // No clear winner here... No mode
        // reach the threshold.
    default:
        $isInterlaced = false;                    
}

// Or get the stats
$stats = $InterlaceDetectGuess->getStats();

// Then decide to apply a de-interlace filter to thumbnail generation
// or video conversion.

```

---------------------------
## Configuration


### Usage with psr/container

> MediaTools provides factories that can be easily used with any for psr-11 compatible container.
> See the [ConfigProvider](./src/Config/ConfigProvider.php) class to get an idea of how 
> service registration can be achieved.  

### With zend-service-manager.

```php
<?php declare(strict_types=1);

use Soluble\MediaTools\Config\ConfigProvider;
use Soluble\MediaTools\Video\ConversionServiceInterface;
use Soluble\MediaTools\Video\InfoServiceInterface;
use Soluble\MediaTools\Video\ThumbServiceInterface;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Zend\ServiceManager\ServiceManager;

soluble-mediatools.config.php
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
        'conversion.threads'      => null,   // <null>: single thread; <0>: number of cores, <1+>: number of threads
        'conversion.timeout'      => null,   // <null>: no timeout, <number>: number of seconds before timing-out
        'conversion.idle_timeout' => 60,     // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'conversion.env'          => []      // An array of additional env vars to set when running the ffmpeg conversion process        
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

$videoConverter   = $container->get(ConversionServiceInterface::class);
$videoInfoService = $container->get(InfoServiceInterface::class);
$videoThumbnailer = $container->get(ThumbServiceInterface::class);
$videoDetection   = $container->get(DetectionServiceInterface::class);

```
  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

