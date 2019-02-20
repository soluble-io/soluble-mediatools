path: blob/master/src
source: Video/VideoInfoReader.php

The ==VideoInfoReader service== acts as a wrapper over **ffprobe** and return information about a video file.
  
```php
<?php
use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\VideoInfoInterface;

$infoReader = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));

try {
    $videoInfo = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // see below for exceptions
}

// Total duration
$duration = $videoInfo->getDuration();

$filesize     = $videoInfo->getFileSize();

// ffprobe format: i.e 'mov,mp4,m4a,3gp,3g2,mj2'
$format       = $videoInfo->getFormatName();


// Get video streams (generally one, i.e mkv containers can have multiple)

$videoStreams = $videoInfo->getVideoStreams();

if (count($videoStreams->count() === 1)) {
    $videoStreams->getFirst()->getCodecName(); // vp9
    $videoStreams->getFirst()->getCodecTagString();
    $videoStreams->getFirst()->getNbFrames();
    $videoStreams->getFirst()->getHeight();
    $videoStreams->getFirst()->getWidth();
    $videoStreams->getFirst()->getDuration();
    $videoStreams->getFirst()->getDurationTs();
    
    $videoStreams->getFirst()->getPixFmt();
    $videoStreams->getFirst()->getNbFrames();
    $videoStreams->getFirst()->getTimeBase();
    $videoStreams->getFirst()->getBitRate();
    $videoStreams->getFirst()->getTags();
    $videoStreams->getFirst()->getDisplayAspectRatio();
    $videoStreams->getFirst()->getSampleAspectRatio();
    $videoStreams->getFirst()->getCodedWidth();
    $videoStreams->getFirst()->getCodedHeight();
    $videoStreams->getFirst()->getRFrameRate();
}


$audioStreams = $videoInfo->getAudioStreams();

if (count($audioStreams->count() === 1)) {
    $audioStreams->getFirst()->getCodecName(); // aac
    $audioStreams->getFirst()->getCodecTagString();
    $audioStreams->getFirst()->getDuration();
    $audioStreams->getFirst()->getDurationTs();
    
    $audioStreams->getFirst()->getTimeBase();
    $audioStreams->getFirst()->getBitRate();
    $audioStreams->getFirst()->getTags();
}




// Count all streams present (audio/video/data)
$nbStreams      = $videoInfo->countStreams();
$nbVideoStreams = $videoInfo->countStreams(VideoInfoInterface::STREAM_TYPE_VIDEO);
$nbAudioStreams = $videoInfo->countStreams(VideoInfoInterface::STREAM_TYPE_AUDIO);

// Advanced, return what ffprobe returned
$metadata      = $videoInfo->getMetadata();
$audioMetadata = $videoInfo->getStreamsMetadataByType(VideoInfoInterface::STREAM_TYPE_VIDEO);
$videoMetadata = $videoInfo->getStreamsMetadataByType(VideoInfoInterface::STREAM_TYPE_AUDIO);
$dataMetadata  = $videoInfo->getStreamsMetadataByType(VideoInfoInterface::STREAM_TYPE_DATA);

``` 



### Requirements

You'll need to have ffprobe [installed](./install-ffmpeg.md) on your system.

### Initialization

[VideoInfoReader](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoInfoReader.php) 
requires an [`FFProbeConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFProbeConfig.php) 
object as first parameter. 
This is where you set the location of the ffprobe binary, the number of threads you allow for conversions
and the various timeouts if needed. The second parameter can be used to inject any psr-3 compatible ==logger==. 

```php
<?php
use Soluble\MediaTools\Video\Config\{FFProbeConfig, FFProbeConfigInterface};
use Soluble\MediaTools\Video\VideoInfoReader;

$converter = new VideoInfoReader(    
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
    Depending on available framework integrations, you may have a look to the [`Video\VideoInfoReaderFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoInfoReaderFactory.php)
    and/or [`FFProbeConfigFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFProbeConfigFactory.php) to get an example based on a psr-11 compatible container.
    See also the provided default [configuration](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file.
               
       
### Exceptions

All info exceptions implements [`InfoReaderExceptionInterface`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Exception/InfoReaderExceptionInterface.php) interface.


```php
<?php
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\Exception as VE;

/** @var VideoInfoReader $vis */
try {
    
    $info = $vis->getInfo('/path/video.mov');
    
    
// All exception below implements VE\InfoReaderExceptionInterface
        
} catch(VE\MissingInputFileException $e) {
    
    // 'video.mov does not exists
    
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


### Metadata

The `VideoInfo::getMetadata()` returns the ffprobe result, if you're 
wondering what it is, have a look to an example with ffprobe 4.0.

> **Warning**, direct use of ffprobe metadata if not ensured by semver, bc break can potentially happen.

```php
<?php

return [
    'streams' => [
        0 => [
            'index' => 0,
            'codec_name' => 'h264',
            'codec_long_name' => 'H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10',
            'profile' => 'Main',
            'codec_type' => 'video',
            'codec_time_base' => '81/2968',
            'codec_tag_string' => 'avc1',
            'codec_tag' => '0x31637661',
            'width' => 320,
            'height' => 180,
            'coded_width' => 320,
            'coded_height' => 180,
            'has_b_frames' => 2,
            'sample_aspect_ratio' => '1:1',
            'display_aspect_ratio' => '16:9',
            'pix_fmt' => 'yuv420p',
            'level' => 40,
            'color_range' => 'tv',
            'color_space' => 'smpte170m',
            'color_transfer' => 'bt709',
            'color_primaries' => 'smpte170m',
            'chroma_location' => 'left',
            'refs' => 1,
            'is_avc' => 'true',
            'nal_length_size' => '4',
            'r_frame_rate' => '120/1',
            'avg_frame_rate' => '1484/81',
            'time_base' => '1/90000',
            'start_pts' => 0,
            'start_time' => '0.000000',
            'duration_ts' => 5467500,
            'duration' => '60.750000',
            'bit_rate' => '39933',
            'bits_per_raw_sample' => '8',
            'nb_frames' => '1113',
            'disposition' => [
                'default' => 1,
                'dub' => 0,
                'original' => 0,
                'comment' => 0,
                'lyrics' => 0,
                'karaoke' => 0,
                'forced' => 0,
                'hearing_impaired' => 0,
                'visual_impaired' => 0,
                'clean_effects' => 0,
                'attached_pic' => 0,
                'timed_thumbnails' => 0,
            ],
            'tags' => [
                'creation_time' => '2018-07-04T14:51:24.000000Z',
                'language' => 'und',
                'handler_name' => 'VideoHandler',
            ],
        ],
        1 => [
            'index' => 1,
            'codec_name' => 'aac',
            'codec_long_name' => 'AAC (Advanced Audio Coding)',
            'profile' => 'LC',
            'codec_type' => 'audio',
            'codec_time_base' => '1/22050',
            'codec_tag_string' => 'mp4a',
            'codec_tag' => '0x6134706d',
            'sample_fmt' => 'fltp',
            'sample_rate' => '22050',
            'channels' => 1,
            'channel_layout' => 'mono',
            'bits_per_sample' => 0,
            'r_frame_rate' => '0/0',
            'avg_frame_rate' => '0/0',
            'time_base' => '1/22050',
            'start_pts' => 0,
            'start_time' => '0.000000',
            'duration_ts' => 1355766,
            'duration' => '61.485986',
            'bit_rate' => '84255',
            'max_bit_rate' => '84255',
            'nb_frames' => '1325',
            'disposition' => [
                'default' => 1,
                'dub' => 0,
                'original' => 0,
                'comment' => 0,
                'lyrics' => 0,
                'karaoke' => 0,
                'forced' => 0,
                'hearing_impaired' => 0,
                'visual_impaired' => 0,
                'clean_effects' => 0,
                'attached_pic' => 0,
                'timed_thumbnails' => 0,
            ],
            'tags' => [
                'creation_time' => '2018-07-04T14:51:24.000000Z',
                'language' => 'eng',
                'handler_name' => 'Mono',
            ],
        ],
    ],
    'format' => [
        'filename' => '/tmp/big_buck_bunny_low.m4v',
        'nb_streams' => 2,
        'nb_programs' => 0,
        'format_name' => 'mov,mp4,m4a,3gp,3g2,mj2',
        'format_long_name' => 'QuickTime / MOV',
        'start_time' => '0.000000',
        'duration' => '61.533000',
        'size' => '983115',
        'bit_rate' => '127816',
        'probe_score' => 100,
        'tags' => [
            'major_brand' => 'mp42',
            'minor_version' => '512',
            'compatible_brands' => 'isomiso2avc1mp41',
            'creation_time' => '2018-07-04T14:51:24.000000Z',
            'title' => 'big_buck_bunny',
            'encoder' => 'HandBrake 1.1.0 2018042400',
        ],
    ]
];

```
