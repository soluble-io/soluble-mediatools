path: blob/master/src
source: Video/VideoConverter.php

The ==VideoConverter service== acts as a wrapper over ffmpeg and
helps with video conversions, clipping, filters, scaling... 

It exposes an immutable api for conversion parameters and attempt to make 
debugging easier with clean exceptions. You can also inject any psr-3 compatible 
logger if you don't want to log issues by yourself.
   

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

### Requirements

You'll need to have ffmpeg [installed](./install-ffmpeg.md) on your system.

### Initialization

The [VideoConverter](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoConverter.php) 
requires an [`FFMpegConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfig.php) 
object as first parameter. 
This is where you set the location of the ffmpeg binary, the number of threads you allow for conversions
and the various timeouts if needed. The second parameter can be used to inject any psr-3 compatible ==logger==. 

```php
<?php
use Soluble\MediaTools\Video\Config\{FFMpegConfig, FFMpegConfigInterface};
use Soluble\MediaTools\Video\VideoConverter;

$converter = new VideoConverter(    
    // @param FFMpegConfigInterface 
    new FFMpegConfig(
        // (?string) - path to ffmpeg binary (default: ffmpeg/ffmpeg.exe)
        $binary = null,
        // (?int)    - ffmpeg default threads (null: single-thread)
        $threads = null,
        // (?float)  - max time in seconds for ffmpeg process (null: disable)
        $timeout = null, 
        // (?float)  - max idle time in seconds for ffmpeg process
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
    Depending on available framework integrations, you may have a look to the [`VideoConverterFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoConverterFactory.php)
    and/or [`FFMpegConfigFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfigFactory.php) to get an example based on a psr-11 compatible container.
    See also the provided default [configuration](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file.
               
### Usage

#### Conversion

Typically you'll use the `VideoConverter::convert()` method in which you specify the input/output files
as well as the conversion params. 

```php
<?php
$conversionService->convert(
    '/path/inputFile.mov', 
     // Output file will be automatically 'shell' escaped,
    '/path/outputFile.mp4', 
    (new VideoConvertParams())->withVideoCodec('libx264')
);           
``` 

*The `convert()` method will automatically set the process timeouts, logger... as specified during 
service initialization.* 

??? question "What if I need more control over the process ? (advanced usage)"
    You can use the `VideoConverter::getSymfonyProcess(string $inputFile, string $outputFile, VideoConvertParamsInterface $convertParams, ?ProcessParamsInterface $processParams = null): Process` 
    to get more control on the conversion process. 
    ```php 
    <?php
    $process = $conversionService->getSymfonyProcess(
        '/path/inputFile.mov', 
         '/path/outputFile.mp4', 
          (new VideoConvertParams())->withVideoCodec('libx264')              
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
 
The [`Video\VideoConvertParams`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoConvertParams.php) 
exposes an immutable api that attempt to mimic ffmpeg params.  
   
```php
<?php
use Soluble\MediaTools\Video\VideoConvertParams;

$params = (new VideoConvertParams())
    ->withVideoCodec('libx264')
    ->withStreamable(true)  
    ->withCrf(24)         
    ->withPreset('fast')
    ->withAudioCodec('aac')
    ->withAudioBitrate('128k');            

```

??? question "Immutable api, what does it change for me ? (vs fluent)"
    VideoConvertParams exposes an ==immutable== style api *(`->withXXX()`, like PSR-7 for example)*.
    It means that the original object is never touched, the `withXXX()` methods will return a newly 
    created object. 
    
    Please be aware of it especially if you're used to ~==fluent==~ interfaces as both expose 
    chainable methods... your primary reflexes might cause pain: 
    
            
    ```php hl_lines="6 9"
    <?php
    $params = (new VideoConvertParams());
    
    $newParams = $params->withVideoCodec('libx264');
    
    // $params used here are empty (incorrect usage)
    $converter->convert('i.mov', 'output', $params);
    
    // $newParams have been initialized with video codec (correct)
    $converter->convert('i.mov', 'output', $newParams);     
    
    ```

Here's a list of categorized built-in methods you can use. See the ffmpeg doc for more information. 

- Video options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withVideoCodec(string)`      | -c:v ◌                 | libx264…   | any supported ffmpeg codec   |
| `withVideoBitrate(string)`    | -b:v ◌                 | 750k,2M…   | constant bit rate            |
| `withVideoMinBitrate(string)` | -minrate ◌             | 750k,2M…   | min variable bitrate         |
| `withVideoMaxBitrate(string)` | -maxrate ◌             | 750k,2M…   | max variable bitrate         |
| `withCrf(int)`                | -crf ◌                 | 32,…       | constant rate factor         |
| `withStreamable()`            | -movflags +faststart   |            | *mp4 container only*         |
| `withPixFmt(string)`          | -pix_fmt ◌             | yuv420p    | Default '*no change*'        |
| `withQuality(string)`         | -quality ◌             | good,medium… |  |
| `withPreset(string)`          | -preset ◌              | fast…        |  |
| `withTune(string)`            | -tune ◌                | film…        |  |
| `withVideoQualityScale(int)`  | -qscale:v ◌            |              |  |
| `withTileColumns(int)`        | -tile-columns ◌        | 10…        | *vp9 related*                |
| `withKeyframeSpacing(int)`    | -g ◌                   | 240…       | *vp9 related*                |
| `withFrameParallel(int)`      | -frame-parallel ◌      | 2…         | *vp9 related*                |
| `withLagInFrames(int)`        | -lag-in-frames ◌       | 25         | vp9, use with `autoAltRef`   |
| `withAutoAltRef(int)`         | -auto-alt-ref ◌        | 1          | vp9, use with `lagInFrames`  |

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

- Filter related: 

| Method                              | FFmpeg arg(s)  | Example(s) | Note(s)                      |
| ----------------------------------- | -------------- | ---------- | ---------------------------- |
| `withFilter(VideoFilterInterface)`  | -filter:v ◌    |            | See doc section about filters|


- General process options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                              |
| ----------------------------- | ---------------------- | ---------- | ------------------------------------ |
| `withSpeed(int)`              | -speed ◌               | 1,2,3…     | *for vp9 or multipass*               |
| `withThreads(int)`            | -threads ◌             | 0,1,2…     | by default uses FFMpegConfig         |
| `withOutputFormat(string)`    | -format ◌              | mp4,webm…  | file extension *(if not provided)*   |
| `withOverwrite()`             | -y                     |            | by default. overwrite if file exists |
| `withNoOverwrite()`           |                        |            | throw exception if output exists     |

- Multipass related

| Method                        | FFmpeg arg(s)          | Example(s)   | Note(s)                                     |
| ----------------------------- | ---------------------- | ------------ | ------------------------------------------- |
| `withPassLogFile(string)`     | -passlogfile ◌         |              | Ex: `tempnam(sys_get_temp_dir(), 'ffmpeg-log') |
| `withPass(int)`               | -pass ◌                | 1 or 2       |                                             |

- Other methods:

| Method                            | Note(s)                              |
| --------------------------------- | ------------------------------------ | 
| `withConvertParam(VideoConvertParamInterface)` | With extra `VideoConvertParams` (will be merged)  | 
| `withBuiltInParam(string, mixed)` | With any supported built-in param, see [constants](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoConvertParamsInterface.php).  | 
| `withoutParam(string)`            | Without the specified parameter. |
| `getParam(string $param): mixed`  | Return the param calue or throw UnsetParamExeption if not set.      |
| `hasParam(string $param): bool`   | Whether the param has been set.  |
| `toArray(): array`                | Return the object as array.      |


> To get the latest list of built-ins, see the 
> [VideoConvertParamsInterface](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoConvertParamsInterface.php) and 
> [FFMpegAdapter](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Adapter/FFMpegAdapter.php) sources.



#### Filters

Video filters can be set to the VideoConvertParams through the `->withVideoFilter(VideoFilterInterface $videoFilter)` method:

```php
<?php
use Soluble\MediaTools\Video\Filter;

$params = (new VideoConvertParams())
    ->withVideoFilter(
        new Filter\VideoFilterChain([
            // A scaling filter
            new Filter\ScaleFilter(800, 600),
            // A denoise filter
            new Filter\Hqdn3DVideoFilter()
        ])
    );

```

See the **[complete video filters doc here](./video-filters.md)** 
       
#### Exceptions

All conversion exceptions implements `Soluble\MediaTools\VideoException\ConverterExceptionInterface`,  interface.,
alternatively you can also :

```php
<?php
use Soluble\MediaTools\Video\{VideoConverter, VideoConvertParams};
use Soluble\MediaTools\Video\Exception as VE;

/** @var VideoConverter $converter */
$params = (new VideoConvertParams())->withVideoCodec('xxx');     
try {
    
    $converter->convert('i.mov', 'o.mp4', $params);
    
// All exception below implements VE\ConverterExceptionInterface
        
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
    
} catch(VE\ConverterExceptionInterface $e) {
    
    // Other exceptions can be
    //
    // - VE\RuntimeException
    // - VE\InvalidParamException (should not happen)
}
       
``` 

### Notes

#### Compression

Achieving a good level of compression while preserving quality is not that easy.  

Compression techniques will depend on the codec (h264, av1, vp9), the purpose (archive, streaming, vod...) 
and the size *(and fps)* of the original content. The mediatools `VideoConverter` is agnostic
and does not offer any help, you'll need to set up your own set of parameters.   

There's a lot of ffmpeg recipes on internet that you can easily port, some interesting sources:

- FFMpeg [VP9 encoding](https://trac.ffmpeg.org/wiki/Encode/VP9)
- FFMpeg [H.264 encoding](https://trac.ffmpeg.org/wiki/Encode/H.264)
- Google [VOD VP9 setting](https://developers.google.com/media/vp9/settings/vod/) 

 
???+ tip "Variable or Constant Bitrate ?  Or both ?"
    
    > *tl;dr*: VBR and CBR can be set together to ensure max quality within a target bitrate (streamability++).    
        
    **Variable Bitrate**

    Variable bitrate (VBR) ensure that you’d achieve the lowest possible file size at the highest possible 
    quality under the given constraints.  
    
    Use the `VideoConvertParams::withBitrate()`, `withMaxBitrate()` and `withMinBitrate()` methods
    to set what you want to achieve. But be warned bitrates must not be set blindly, to be effective 
    they must be choosen in respect to video dimensions and fps. See [VOD VP9 setting](https://developers.google.com/media/vp9/settings/vod/). 
        
    **Constant Bitrate**
    
    The `VideoConvertParams::withCrf()` will set the Constant Rate Factor (CRF) setting 
    for the x264, x265 and vp9 encoders.
    
    - **h26x:** You can set the values between 0 and 51, where lower values would result in better quality,
            at the expense of higher file sizes. Higher values mean more compression,
            but at some point you will notice the quality degradation.
            For x264, sane values are between 18 and 28. The default is 23, so you can use this as a starting point.
    
    - **vpx:**  The CRF value can be from 0–63. Lower values mean better quality. Recommended values range from 15–35,
            with 31 being recommended for 1080p HD video
            
    > *Please also be sure to understand what rate control modes are 
    > (you can see [here](https://slhck.info/video/2017/03/01/rate-control.html) 
    > and [here](https://slhck.info/video/2017/02/24/crf-guide.html) and how to choose the one you need.*  
        

#### Performance

Conversions are heavy dudes, things that can help:
 
- Increasing the FfmpegConfig threads parameter can help for some tasks.
- Order of parameters can help. i.e: if you need to clip, makes it before applying filters. 

### Recipes

#### Transcode to `mp4/x264/aac`

> See the [official H264](https://trac.ffmpeg.org/wiki/Encode/H.264) doc. 

```php
<?php
use Soluble\MediaTools\Video\{Exception, VideoConvertParams};
use Soluble\MediaTools\Video\VideoConverterInterface;

$params = (new VideoConvertParams())
    ->withVideoCodec('libx264')
    ->withAudioCodec('aac')
    ->withAudioBitrate('128k')            
    ->withStreamable(true)      // Add streamable options (movflags & faststart) 
    ->withCrf(24)               // Level of compression: better size / less visual quality  
    ->withPreset('fast');       // Optional: see presets           
    
try {
    
    /** @var VideoConverterInterface $converter */
    
    $converter->convert(
        '/path/inputFile.mov', 
        '/path/outputFile.mp4', 
        $params
    );
    
} catch(Exception\ConverterExceptionInterface $e) {
    // See chapters about exception !!! 
   
}
       
``` 

#### Transcode to `webm/vp9/opus`

> See the official [ffmpeg VP9 docs](https://trac.ffmpeg.org/wiki/Encode/VP9) 
> and have a look at the [google vp9 VOD](https://developers.google.com/media/vp9/settings/vod/#ffmpeg_command_lines) guidelines


```php
<?php
use Soluble\MediaTools\Video\{Exception, VideoConvertParams};
use Soluble\MediaTools\Video\VideoConverterInterface;

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
    ->withSpeed(1)
    // Optional: Use videoprobe to be sure of color conversions if any needed
    // ->withPixFmt('yuv420p') 
    ->withOutputFormat('webm');


try {
    
    /** @var VideoConverterInterface $converter */
    
    $converter->convert(
        '/path/inputFile.mov', 
        '/path/outputFile.webm', 
        $params
    );
    
} catch(Exception\ConverterExceptionInterface $e) {
    // see chapter about exceptions        
} 

``` 

#### Video scaling

> See also [ffmpeg doc](https://trac.ffmpeg.org/wiki/Scaling)

```php
<?php
use Soluble\MediaTools\Video\{Exception, VideoConvertParams, SeekTime};
use Soluble\MediaTools\Video\Filter\ScaleFilter;

$params = (new VideoConvertParams())
          ->withVideoFilter(
                new ScaleFilter(
                    // $width:  as an int or any ffmpeg supported placeholder: iw*0.5, ...
                    800,
                    // $height:  as an int or any ffmpeg supported placeholder: ih*0.5, ...
                    'ih*0.5',
                    // $aspect_ratio_mode (increase or decrease)
                    ScaleFilter::OPTION_ASPECT_RATIO_INCREASE
                ) 
          );
                          
try {
    /** @var \Soluble\MediaTools\Video\VideoConverterInterface $videoConverter */
    $videoConverter->convert(
        '/path/inputFile.mp4', 
        '/path/outputFile.mp4', 
        $params
    );
} catch(Exception\ConverterExceptionInterface $e) {
    // see chapter about exceptions        
}
```

#### Video clipping

> See the official [ffmpeg docs](https://trac.ffmpeg.org/wiki/Seeking) 


```php
<?php
use Soluble\MediaTools\Video\{Exception, VideoConvertParams, SeekTime};

$params = (new VideoConvertParams())
          ->withSeekStart(new SeekTime(10.242)) // 10 sec, 242 milli
          ->withSeekEnd(SeekTime::createFromHMS('12:52.015')); // 12 mins, 52 secs...                

try {
    /** @var \Soluble\MediaTools\Video\VideoConverterInterface $videoConverter */
    $videoConverter->convert(
        '/path/inputFile.mp4', 
        '/path/outputFile.mp4', 
        $params
    );
} catch(Exception\ConverterExceptionInterface $e) {
    // see chapter about exceptions        
}

``` 


#### Multipass encoding

```php
<?php
use Soluble\MediaTools\Video\{VideoConvertParams, VideoConvertParamsInterface};
use Soluble\MediaTools\Common\IO\PlatformNullFile;

// Where to store the result of first pass analysis

$logFile = tempnam(sys_get_temp_dir(), 'ffmpeg-passlog');

$pass1Params = (new VideoConvertParams())    
    ->withVideoCodec('libvpx-vp9')
    ->withVideoBitrate('1M') 
    ->withVideoMaxBitrate('1500k') 
    ->withVideoMinBitrate('750k')
    ->withKeyframeSpacing(240)
    ->withTileColumns(1)
    ->withFrameParallel(1)    
    // Set the pass number
    ->withPass(1)
    // Set the ffmpeg logfile 
    ->withPassLogFile($logFile)
    // Speed in first pass can be faster
    ->withSpeed(4)
    // Audio does not need to be analyzed
    ->withNoAudio()
    // Because we will pipe it to /dev/null
    // we need to specify container
    ->withOutputFormat('webm');


// PASS 1 Conversion
$this->videoConvert->convert(
        '/tmp/input.mov',
        // In first pass we don't need to output the conversion result
        // let's put in /dev/null. 
        new PlatformNullFile(),
        $pass1Params
);

// Let's init pass 2 params from pass 1
$pass2Params = $pass1Params    
    // reinit audio    
    ->withoutParam(VideoConvertParamsInterface::PARAM_NOAUDIO)    
    ->withAudioCodec('libopus')
    ->withAudioBitrate('256k')
    // Reset the pass number
    ->withPass(2)
    // Speed in second pass must be slower
    ->withSpeed(1);
    

$this->videoConvert->convert(
    '/tmp/input.mov',
    '/tmp/output.webm',
    $pass2Params
);

```

