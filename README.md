# soluble-mediatools  

[![PHP Version](http://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Media tools, toolbax for media processing, transcoding...

## Status  

**Early stages**

## Features

- Video thumbnailing
- Video transcoding
- Video detection

## Requirements

- PHP 7.1+
- FFmpeg 3.4+, 4.0+




## Examples



### Transcoding

mp4/h264/aac

```php
<?php
use Soluble\MediaTools\VideoTranscode;
use Soluble\MediaTools\VideoTranscodeParams;
use Soluble\MediaTools\VideoProbe;

// Use 
$videoTranscode = new VideoTranscode($ffmpegConfig[], $videoProbe=(new VideoProbe(null, null))); 

$file = '/path/test.mov';

// Optional, whether the source is interlaced ?
$videoFilters = $videoTranscode->getDeintFilter($file);

$params = (new VideoTranscodeParams())
            ->withVideoCodec('h264')
            ->withAudioCodec('aac')
            ->withAudioBitrate('128k')
            ->withPreset('medium')
            ->withStreamable(true)
            ->withCrf(24)
            ->withOutputFormat('mp4');

$videoTranscode->transcode($file, "$file.mp4", $params, $videoFilters);

``` 

webm/vp9/opus

```php
<?php
use Soluble\MediaTools\VideoTranscode;
use Soluble\MediaTools\VideoTranscodeParams;
use Soluble\MediaTools\VideoProbe;

// Use 
$videoTranscode = new VideoTranscode($ffmpegConfig[], $videoProbe=(new VideoProbe(null, null))); 

$file = '/path/test.mov';

// Optional, whether the source is interlaced ?
$videoFilters = $videoTranscode->getDeintFilter($file);

$params = (new VideoTranscodeParams())
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


$videoTranscode->transcode($file, "$file.webm", $params, $videoFilters);

``` 

## Config

### Via container (psr/container compat)

```php
<?php declare(strict_types=1);

use Soluble\MediaTools\VideoProbeFactory;
use Soluble\MediaTools\VideoTranscodeFactory;
use Soluble\MediaTools\VideoThumbFactory;
use Zend\ServiceManager\ServiceManager;

$config = [
    'soluble-mediatools' => [
        'ffmpeg.threads'   => null, // do not set any threads: 0 means all cores
        'ffmpeg.binary'  => 'ffmpeg', // or a complete path /opt/local/ffmpeg/bin/
        'ffprobe.binary' => 'ffprobe', // or a complete path /opt/local/ffmpeg/bin/
    ],    
];


$container = (new ServiceManager(['config' => $config]));

$videoProbe     = (new VideoProbeFactory())->__invoke($container);
$videoTranscode = (new VideoTranscodeFactory())->__invoke($container);
$videoThumb     = (new VideoThumbFactory())->__invoke($container);

```



### Programmatic (non-recommended)

```php
<?php declare(strict_types=1);

use Soluble\MediaTools\Config\FFMpegConfig;
use Soluble\MediaTools\Config\FFProbeConfig;
$ffmpegConfig = new FFMpegConfig('/usr/bin/ffmpeg');
$fprobeConfig = new FFProbeConfig('/usr/bin/ffprobe');

```

  
## Coding standards and interop

* [PSR 7 HTTP Message](https://github.com/php-fig/http-message)
* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

