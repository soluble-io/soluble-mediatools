hero: Video information/query service
path: blob/master/src
source: Video/InfoService.php

### Overview

The ==Video\InfoService== acts as a wrapper over ffprobe and return information about a video file.

It relies on the [symfony/process](https://symfony.com/doc/current/components/process.html) 
component and attempt to make debugging easier with clean exceptions. You can also inject any psr-3 compatible 
logger if you don't want to log issues by yourself.    
  

//```php hl_lines="8 9 10 11 14 15 16 17 18"
```php
<?php
use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\ConversionExceptionInterface;
use Soluble\MediaTools\Video\InfoService;

$infoService = new InfoService(new FFProbeConfig('/path/to/ffprobe'));

$videoInfo = $infoService->getInfo('/path/video.mp4');

$videoInfo->getDuration();
$videoInfo->getNbFrames();
$videoInfo->getDimensions();
       
``` 

### Requirements

You'll need to have ffprobe installed on your system.

### Initialization

The [Video\InfoService](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/InfoService.php) 
requires an [`FFProbeConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFProbeConfig.php) 
object as first parameter. 
This is where you set the location of the ffprobe binary, the number of threads you allow for conversions
and the various timeouts if needed. The second parameter can be used to inject any psr-3 compatible ==logger==. 

```php
<?php
use Soluble\MediaTools\Video\Config\{FFProbeConfig, FFProbeConfigInterface};
use Soluble\MediaTools\Video\InfoService;

$vcs = new InfoService(    
    // @param FFProbeConfigInterface 
    new FFProbeConfig(
        // (?string) - path to ffprobe binary (default: ffprobe/ffprobe.exe)
        $binary = null,
        // (?float)  - max time in seconds for ffprobe process (null: disable)
        $timeout = null, 
        // (?float)  - max idle time in seconds for ffprobe process
        $idleTimeout = null, 
        // (array)   - additional environment variables
        $env = []                           
    ),
    // @param ?\Psr\Log\LoggerInterface - Default to `\Psr\Log\NullLogger`.     
    $logger = null   
);
```

??? tip "Tip: initialize in a container (psr-11)" 
    It's a good idea to register services in a container. 
    Depending on available framework integrations, you may have a look to the [`Video\InfoServiceFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/InfoServiceFactory.php)
    and/or [`FFProbeConfigFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFProbeConfigFactory.php) to get an example based on a psr-11 compatible container.
    See also the provided default [configuration](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file.
               
### Usage

#### Conversion
 
The `Video\ConversionService` offers a quick and simple `convert()` method in which you specify the input/output files
as well as the conversion params. 

```php
<?php
$conversionService->convert(
    '/path/inputFile.mov', 
    '/path/outputFile.mp4', 
    (new ConversionParams())->withVideoCodec('libx264')
);           
``` 

*The `convert()` method will automatically set the process timeouts, logger... as specified during 
service initialization.* 

??? question "What if I need more control over the process ? (advanced usage)"
    You can use the `Video\ConversionService::getSymfonyProcess(string $inputFile, string $outputFile, ConversionParamsInterface $convertParams, ?ProcessParamsInterface $processParams = null): Process` 
    to get more control on the conversion process. 
    ```php 
    <?php
    $process = $conversionService->getSymfonyProcess(
        '/path/inputFile.mov', 
         '/path/outputFile.mp4', 
          (new ConversionParams())->withVideoCodec('libx264')              
    );
    
    $process->start();

    foreach ($process as $type => $data) {
        if ($process::OUT === $type) {
            echo "\nRead from stdout: ".$data;
        } else { // $process::ERR === $type
            echo "\nRead from stderr: ".$data;
        }
    }        
    ```
    Have a look to the [symfony/process documentation](https://symfony.com/doc/current/components/process.html) for more recipes. 

#### Parameters
 
The [`Video\ConversionParams`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionParams.php) 
exposes an immutable api that attempt to mimic ffmpeg params.  
   
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

??? question "Immutable api, what does it change for me ? (vs fluent)"
    ConversionParams exposes an ==immutable== style api *(`->withXXX()`, like PSR-7 for example)*.
    It means that the original object is never touched, the `withXXX()` methods will return a newly 
    created object. 
    
    Please be aware of it especially if you're used to ~==fluent==~ interfaces as both expose 
    chainable methods... your primary reflexes might cause pain: 
    
            
    ```php hl_lines="6 9"
    <?php
    $params = (new ConversionParams());
    
    $newParams = $params->withVideoCodec('libx264');
    
    // $params used here are empty (incorrect usage)
    $vcs->convert('i.mov', 'output', $params);
    
    // $newParams have been initialized with video codec (correct)
    $vcs->convert('i.mov', 'output', $newParams);     
    
    ```

Here's a list of categorized built-in methods you can use. See the ffmpeg doc for more information. 

- Video options:

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

- Audio options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withAudioCodec(string)`      | -c:a ◌                 | aac,mp3…   | *webm* requires vorbis/opus  |
| `withAudioBitrate(string)`    | -b:a ◌                 | 128k…      |                              |  
| `withNoAudio()`               | -an                    |            | removes all audio tracks     |

- Seeking/clipping options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withSeekStart(SeekTime)`     | -ss ◌                  | [`SeekTime::createFromHms('0:00:01.9')`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/SeekTime.php) |   |
| `withSeekEnd(SeekTime)`       | -to ◌                  | `new SeekTime(120.456)`                |                              |
| `withVideoFrames(int)`        | -frames:v ◌            | 1000…      | Only ◌ frames   |


- General process options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                              |
| ----------------------------- | ---------------------- | ---------- | ------------------------------------ |
| `withSpeed(int)`              | -speed ◌               | 1,2,3…     | *for vp9 or multipass*               |
| `withThreads(int)`            | -threads ◌             | 0,1,2…     | by default uses FFMpegConfig         |
| `withOutputFormat(string)`    | -format ◌              | mp4,webm…  | file extension *(if not provided)*   |
| `withOverwrite()`             | -y                     |            | by default. overwrite if file exists |
| `withNoOverwrite()`           |                        |            | throw exception if output exists     |

- Other methods:

| Method                            | Note(s)                              |
| --------------------------------- | ------------------------------------ | 
| `withBuiltInParam(string, mixed)` | With any supported built-in param, see [constants](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionParamsInterface.php).  | 
| `withoutParam(string)`            | Without the specified parameter. |
| `getParam(string $param): mixed`  | Return the param calue or throw UnsetParamExeption if not set.      |
| `hasParam(string $param): bool`   | Whether the param has been set.  |
| `toArray(): array`                | Return the object as array.      |


> To get the latest list of built-ins, see the 
> [ConversionParamsInterface](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ConversionParamsInterface.php) and 
> [FFMpegAdapter](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Adapter/FFMpegAdapter.php) sources.


#### Filters

Video filters can be set to the ConversionParams through the `withVideoFilter(VideoFilterInterface $videoFilter)` method:

```php
<?php
use Soluble\MediaTools\Video\Filter;

$params = (new ConversionParams())
    ->withVideoFilter(
        // A denoise filter
        new Filter\Hqdn3DVideoFilter()
    );

```

If you need to chain multiple filters, you can use the [`VideoFilterChain`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/VideoFilterChain.php) object:

```php
<?php
use Soluble\MediaTools\Video\Filter;

// from the constructor
$filters = new Filter\VideoFilterChain([    
    new Filter\YadifVideoFilter(),
    new Filter\Hqdn3DVideoFilter() 
]);

// Alternatively, use ->addFilter method
$filters->addFilter(new Filter\NlmeansVideoFilter());

$params = (new ConversionParams())
    ->withVideoFilter($filters);

// ....

```
 
Currently there's only few built-in filters available:

| Filter                   |Note(s)                               |
| ------------------------ | ------------------------------------ | 
| [`YadifVideoFilter`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/YadifVideoFilter.php)       | Deinterlacer  | 
| [`Hqdn3DVideoFilter`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/Hqdn3DVideoFilter.php)       | Basic denoiser  | 
| [`NlmeansFilter`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/NlmeansVideoFilter.php)       | Very slow but good denoiser  | 
 
> But it's quite easy to add yours, simply implements the [FFMpegVideoFilterInterface](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/Type/FFMpegVideoFilterInterface.php).
> We :heart: pull request, so don't forget to share :)
  
  
  
??? Question "Is the order of parameters, filters... important ?" 
    Generally FFMpeg will evaluate the parameters in the order they appear... 
    So if you're about to clip a video (from 1s to 3s) and use a denoise filter, 
    setting the clipping params before the filter will generally be more performant
    *(the denoise filter will only be applied on the clipped part of the video 
    and not it's entire length)*.     
       
#### Exceptions

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
    
// All exception below implements VE\ConversionExceptionInterface
        
} catch(VE\MissingInputFileException $e) {
    
    // 'i.mov does not exists
    
    echo $e->getMessage();    
    
} catch (
    
    // The following 3 exceptions are linked to process
    // failure 'ffmpeg exit code != 0) and implements
    //
    // - `VE\ConversionProcessExceptionInterface`
    //        (* which extends Mediatools\Common\Exception\ProcessExceptionInterface)    
    //
    // you can catch all them at once or separately:
    
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

$params = (new ConversionParams())
          ->withSeekStart(new SeekTime(10.242)) // 10 sec, 242 milli
          ->withSeekEnd(SeekTime::createFromHMS('12:52.015')); // 12 mins, 52 secs...                

try {
    /** @var \Soluble\MediaTools\Video\ConversionServiceInterface $videoConverter */
    $videoConverter->convert(
        '/path/inputFile.mp4', 
        '/path/outputFile.mp4', 
        $params
    );
} catch(Exception\ConversionExceptionInterface $e) {
    // see chapter about exceptions        
}

``` 

