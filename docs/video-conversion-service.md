hero: Video conversion service
path: blob/master/src
source: Video/ConversionService.php

The ==Video\ConversionService== acts as a wrapper over ffmpeg and
helps with video conversions, clipping... 

### Overview

```php
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\ConversionExceptionInterface;
use Soluble\MediaTools\Video\{ConversionService, ConversionParams};

$vcs = new ConversionService(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new ConversionParams())
    ->withVideoCodec('libx264')    
    ->withStreamable(true)
    ->withCrf(24);                  
    
try {    
    $vcs->convert(
        '/path/inputFile.mov', 
        '/path/outputFile.mp4', 
        $params
    );    
} catch(ConversionExceptionInterface $e) {
    // See chapter about exception !!!    
}
       
``` 

### Initialize

The [Video\ConversionService](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionService.php) requires an [`FFMpegConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfig.php) object as first parameter. 
This is where you set the location of the ffmpeg binary, the number of threads you allow for conversions
and the various timeouts if needed. The second parameter can be used to inject any psr-3 compatible ==logger==. 

```php
<?php
use Soluble\MediaTools\Video\Config\{FFMpegConfig, FFMpegConfigInterface};
use Soluble\MediaTools\Video\ConversionService;

$vcs = new ConversionService(    
    // @param FFMpegConfigInterface 
    new FFMpegConfig(
        $binary = null,      // (?string) - path to ffmpeg binary (default: 'ffmpeg' or 'ffmpeg.exe' on Windows)  
        $threads = null,     // (?int)    - ffmpeg default threads (null: single-thread)
        $timeout = null,     // (?float)  - max time in seconds for ffmpeg process (null: disable) 
        $idleTimeout = null, // (?float)  - max idle time in seconds for ffmpeg process
        $env = []            // (array)   - additional environment variables               
    ),
    // @param ?\Psr\Log\LoggerInterface - Default to `\Psr\Log\NullLogger`.     
    $logger = null   
);
```

???+ tip 
    It's a good idea to register services in a container. Depending on available 
    framework integrations, you may have a look to the [`Video\ConversionServiceFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionServiceFactory.php)
    to get an example based on a psr-11 compatible container.
          


### Conversion 

The `Video\ConversionService` offers two methods, the first one `->convert()` allows 

```php
<?php
$conversionService->convert(
    '/path/inputFile.mov', 
    '/path/outputFile.mp4', 
    (new ConversionParams())->withVideoCodec('libx264')
);           
``` 

#### ConversionParams


FFmpeg's command line arguments can quickly become confusing, especially when they have multiple aliases.... 
The [`Video\ConversionParams`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionParams.php) 
expose an ==immutable== interface and attempt to make conversion params as readable as possible: 
   

```php
<?php
use Soluble\MediaTools\Video\ConversionParams;

$params = (new ConversionParams())
    ->withVideoCodec('libx264')
    ->withStreamable(true)  
    ->withCrf(24)         
    ->withPreset('fast')
    ->withAudioCodec('aac')
    ->withAudioBitrate('128k');            

```

???+ warning
    ==ConversionParams== exposes an ==immutable :heart:== style api *(`->withXXX()`, like PSR-7 for example)*.
    It means that the original params are never touched, the `withXXX()` methods will return a newly 
    created object. Please be aware of it especially if you're used to ~==fluent==~ interfaces as both expose 
    chainable methods... your primary reflexes might cause pain: 
    
            
    ```php 
    <?php
    $params = (new ConversionParams());
    
    $newParams = $params->withVideoCodec('libx264')
                        ->withCrf(33);
    
    // The two next lines won't use the same params !!!
    $vcs->convert('i.mov', 'output', $params); 
    $vcs->convert('i.mov', 'output', $newParams);     
    
    ```


#### Built-in params

ConversionParams offers some built-in methods to ffmpeg. To get the latest list of built-ins, see the 
[ConversionParamsInterface](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionParamsInterface.php) and 
[FFMpegAdapter](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Adapter/FFMpegAdapter.php) sources.

Video options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withVideoCodec(string)`      | -c:v ◌                 | libx264…   | any supported ffmpeg codec   |
| `withVideoBitrate(string)`    | -b:v ◌                 | 750k,2M…   | constant bit rate            |
| `withVideoMinBitrate(string)` | -minrate ◌             | 750k,2M…   | min variable bitrate         |
| `withVideoMaxBitrate(string)` | -maxrate ◌             | 750k,2M…   | max variable bitrate         |
| `withCrf(int)`                | -crf ◌                 | 32,…       | constant rate compression    |
| `withStreamable()`            | -movflags +faststart   |            | *mp4 container only*         |
| `withTileColumns(int)`        | -tile-columns ◌        | 10…        | *vp9 related*                |
| `withKeyframeSpacing(int)`    | -g ◌                   | 240…       | *vp9 related*                |
| `withFrameParallel(int)`      | -frame-parallel ◌      | 2…         | *vp9 related*                |
| `withPixFmt(string)`          | -pix_fmt ◌             | yuv420p    | Default '*no change*'        |
| `withQuality(string)`         | -quality ◌             | good,medium… |  |
| `withPreset(string)`          | -preset ◌              | fast…        |  |
| `withTune(string)`            | -tune ◌                | film…        |  |
| `withVideoQualityScale(int)`  | -qscale:v ◌            |              |  |



Audio options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withAudioCodec(string)`      | -c:a ◌                 | aac,mp3…   | *webm* requires vorbis/opus  |
| `withAudioBitrate(string)`    | -b:a ◌                 | 128k…      |                              |  
| `withNoAudio()`               | -an                    |            | removes all audio tracks     |

Seeking/clipping options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withSeekStart(SeekTime)`     | -ss ◌                  | [`SeekTime::createFromHms('0:00:01.9')`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/SeekTime.php) |   |
| `withSeekEnd(SeekTime)`       | -to ◌                  | `new SeekTime(120.456)`                |                              |
| `withVideoFrames(int)`        | -frames:v ◌            | 1000…      | Only ◌ frames   |


General conversion options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                              |
| ----------------------------- | ---------------------- | ---------- | ------------------------------------ |
| `withSpeed(int)`              | -speed ◌               | 1,2,3…     | *for vp9 or multipass*               |
| `withThreads(int)`            | -threads ◌             | 0,1,2…     | by default, use FFMpegConfig         |
| `withOutputFormat(string)`    | -format ◌              | mp4,webm…  | file extension *(if not provided)*   |
| `withOverwrite()`             | -y                     |            | by default. overwrite if file exists |
| `withNoOverwrite()`           |                        |            | throw exception if output exists     |
| `withNoOverwrite()`           |                        |            | throw exception if output exists     |


In some circumstances (deserialization...) you may want to 


### Video filters

todo

### Exception

All conversion exceptions implements `Soluble\MediaTools\VideoException\ConversionExceptionInterface`,  interface.,
alternatively you can also :

```php
<?php
use Soluble\MediaTools\Video\{ConversionService, ConversionParams};
use Soluble\MediaTools\Video\Exception as VE;

/** @var ConversionService $vcs */
$params = (new ConversionParams())->withVideoCodec('xxx');     
try {
    
    $vcs->convert('i.mov', 'o.mp4', $params);
    
// All exception below implements Ve\ConversionExceptionInterface
// It's possible to get them all in once
        
} catch(VE\MissingInputFileException $e) {
    
    // 'i.mov does not exists
    
    echo $e->getMessage();    
    
} catch(
    
    // The following 3 exeptions are linked to process
    // failure 'ffmpeg exit code != 0) and implements
    //
    // - `VE\ConversionProcessExceptionInterface`
    //        (* which extends Mediatools\Common\Exception\ProcessExceptionInterface)    
    //
    // in case you want to catch them all-in-once
    
      VE\ProcessFailedException       
    | VE\ProcessSignaledException
    | VE\ProcessTimedOutException $e) 
{
    
    echo $e->getMessage();
    
    // Because they implement ProcessExceptionInterface
    // we can get a reference to the executed (symfony) process:
    
    $process = $e->getProcess();
    echo $process->getExitCode();
    echo $process->getErrorOutput();
    
} catch(VE\ConversionExceptionInterface $e) {
    
    // Other exceptions can be
    //
    // - VE\RuntimeException
    // - VE\InvalidParamException (should not happen)
}
       
``` 

### Recipes

#### Transcode to `mp4/x264/aac`

> See the [official H264](https://trac.ffmpeg.org/wiki/Encode/H.264) doc. 

```php
<?php
use Soluble\MediaTools\Video\{Exception, ConversionParams};
use Soluble\MediaTools\Video\ConversionServiceInterface;

$params = (new ConversionParams())
    ->withVideoCodec('libx264')
    ->withAudioCodec('aac')
    ->withAudioBitrate('128k')            
    ->withStreamable(true)      // Add streamable options (movflags & faststart) 
    ->withCrf(24)               // Level of compression: better size / less visual quality  
    ->withPreset('fast');       // Optional: see presets           
    
try {
    
    /** @var ConversionServiceInterface $vcs */
    
    $vcs->convert(
        '/path/inputFile.mov', 
        '/path/outputFile.mp4', 
        $params
    );
    
} catch(Exception\ConversionExceptionInterface $e) {
    // See chapters about exception !!! 
   
}
       
``` 

#### Transcode to `webm/vp9/opus`

> See the official [ffmpeg VP9 docs](https://trac.ffmpeg.org/wiki/Encode/VP9) 
> and have a look at the [google vp9 VOD](https://developers.google.com/media/vp9/settings/vod/#ffmpeg_command_lines) guidelines


```php
<?php
use Soluble\MediaTools\Video\{Exception, ConversionParams};
use Soluble\MediaTools\Video\ConversionServiceInterface;

$params = (new ConversionParams())
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
    
    /** @var ConversionServiceInterface $vcs */
    
    $vcs->convert(
        '/path/inputFile.mov', 
        '/path/outputFile.webm', 
        $params
    );
    
} catch(Exception\ConversionExceptionInterface $e) {
    // see chapter about exceptions        
} 

``` 

#### Video clipping

> See the official [ffmpeg docs](https://trac.ffmpeg.org/wiki/Seeking) 


```php
<?php
use Soluble\MediaTools\Video\{Exception, ConversionParams, SeekTime};

$convertParams = (new ConversionParams)
                ->withSeekStart(new SeekTime(10.242)) // 10 sec, 242 milli
                ->withSeekEnd(SeekTime::createFromHMS('12:52.015')); // 12 mins, 52 secs...                

try {
    /** @var \Soluble\MediaTools\Video\ConversionServiceInterface $videoConverter */
    $videoConverter->convert('/path/inputFile.mp4', '/path/outputFile.mp4', $convertParams);
} catch(Exception\ConversionExceptionInterface $e) {
    // see chapter about exceptions        
}

``` 

