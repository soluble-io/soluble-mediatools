
The `Video\ConvertServiceInterface` offers <TODO>


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
>  * @var ContainerInterface         $aPsr11Container 
>  * @var ConversionServiceInterface $videoConverter
>  */ 
> $videoConverter = $aPsr11Container->get(ConversionServiceInterface::class);
> ```

#### Conversion from `mov` to `mp4/x264/aac`

> See the [official H264](https://trac.ffmpeg.org/wiki/Encode/H.264) doc. 

```php
<?php
use Soluble\MediaTools\Video\{Exception, ConversionParams};

$convertParams = (new ConversionParams)
            ->withVideoCodec('libx264')
            ->withAudioCodec('aac')
            ->withAudioBitrate('128k')            
            ->withStreamable(true)      // Add streamable options (movflags & faststart) 
            ->withCrf(24)               // Level of compression: better size / less visual quality  
            ->withPreset('fast');       // Optional: see presets           
    
try {
    
    /** @var Soluble\MediaTools\Video\ConversionServiceInterface $videoConverter */
    $videoConverter->convert('/path/inputFile.mov', '/path/outputFile.mp4', $convertParams);
    
} catch(Exception\ConversionExceptionInterface $e) {
    // See chapters about exception !!! 
   
}
       
``` 

#### Conversion from `mov` to `webm/vp9/opus`

> See the official [ffmpeg VP9 docs](https://trac.ffmpeg.org/wiki/Encode/VP9) 
> and have a look at the [google vp9 VOD](https://developers.google.com/media/vp9/settings/vod/#ffmpeg_command_lines) guidelines


```php
<?php
use Soluble\MediaTools\Video\{Exception, ConversionParams};

$convertParams = (new ConversionParams)
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

