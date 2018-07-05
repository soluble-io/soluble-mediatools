# soluble-mediatools  

[![PHP Version](http://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Media tools: toolbox for media processing, video conversions, transcoding, transmuxing... Wrapper for ffmpeg/ffprobe. 

## Status  

> WIP

## Features

- [x] Video conversion (transcoding, transmuxing...)
- [x] Video thumbnailing (wip)
- [x] Video information (wip)

## Requirements

- PHP 7.1+
- FFmpeg 3.4+, 4.0+

## Examples

### Conversion

> Example of conversion from `mov` to `mp4/h264/aac`

```php
<?php
use Soluble\MediaTools\{VideoConvert, VideoConvertParams, Exception as ConversionException};

/**
 * @var \Psr\Container\ContainerInterface $anyPsr11Container 
 * @var VideoConvert $videoConvert video conversion service
 */ 
$videoConvert = $anyPsr11Container->get(VideoConvert::class); 

$convertParams = (new VideoConvertParams())
            ->withVideoCodec('h264')
            ->withAudioCodec('aac')
            ->withAudioBitrate('128k')
            ->withPreset('medium')
            ->withStreamable(true)
            ->withCrf(24)
            ->withOutputFormat('mp4');
    
try {
    $process = $videoConvert->convert('/path/inputFile.mov', '/path/outputFile.mp4', $convertParams);
} catch (ConversionException\FileNotFoundException $e) {
    // Input file not found
}

if ($process->getExitCode() !== 0) {
    // As an example
    throw new \RuntimeException(
          sprintf(
              "Command '%s' failed with error code '%s', error output: '%s'.",
              $process->getCommandLine(),
              $process->getExitCode(),
              $process->getErrorOutput()
          )
    );        
}

``` 

> Example of conversion from `mov` to `webm/vp9/opus`


```php
<?php
use Soluble\MediaTools\VideoConvert;
use Soluble\MediaTools\VideoConvertParams;
use Soluble\MediaTools\VideoProbe;

// Use 
$videoConvert = new VideoConvert($ffmpegConfig[], $videoProbe=(new VideoProbe(null, null))); 

$file = '/path/test.mov';

// Optional, whether the source is interlaced ?
$videoFilters = $videoConvert->getDeintFilter($file);

$params = (new VideoConvertParams())
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
                ->withSpeed(0)
                // Optional: Use videoprobe to be sure of color conversions if any needed
                // ->withPixFmt('yuv420p') 
                ->withOutputFormat('webm');


$process = $videoConvert->convert($file, "$file.webm", $params, $videoFilters);

if ($process->getExitCode() !== 0) {
    // As an example
    throw new \RuntimeException(
          sprintf(
              "Command '%s' failed with error code '%s', error output: '%s'.",
              $process->getCommandLine(),
              $process->getExitCode(),
              $process->getErrorOutput()
          )
    );        
}


/*
foreach ($process as $type => $data) {
    if ($process::OUT === $type) {
        $stdOut .= $data;
    } else { // $process::ERR === $type
        $stdErr .= $data;
    }
}
*/


``` 

## Configuration

### Usage with psr/container

Example 1: with zend-service-manager.

```php
<?php declare(strict_types=1);

use Soluble\MediaTools\{VideoConvert, VideoProbe, VideoThumb};
use Soluble\MediaTools\Config\ConfigProvider;
use Zend\ServiceManager\ServiceManager;

// Config options can be in a file, i.e: `require 'config/soluble-mediatools.global.php';`
// or set via dotenv...
$config = [
    'soluble-mediatools' => [
        'ffmpeg.threads'   => null, // do not set any threads: 0 means all cores
        'ffmpeg.binary'  => 'ffmpeg', // or a complete path /opt/local/ffmpeg/bin/
        'ffprobe.binary' => 'ffprobe', // or a complete path /opt/local/ffmpeg/bin/
    ],    
];

// Service manager
$container = new ServiceManager(
                array_merge([
                    'services' => [
                        'config' => $config
                    ]],
                    (new ConfigProvider())->getDependencies()
             ));

// Now whenever you want an instance of a service:

$videoProbe     = $container->get(VideoProbe::class);
$VideoConvert = $container->get(VideoConvert::class);
$videoThumb     = $container->get(VideoThumb::class);

```

  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

