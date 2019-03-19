path: blob/master/src
source: Video/VideoInfoReader.php

The ==VideoInfoReader service== acts as a wrapper over **ffprobe** and return information about a video file.


### Requirements

You'll need to have ffprobe [installed](./install-ffmpeg.md) on your system.
  
### Initialization

The [VideoInfoReader](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoInfoReader.php) service 
requires an [`FFProbeConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFProbeConfig.php) 
object as first parameter. If needed you can configure the location of the ffprobe binary and the various timeouts. 

The second parameter can be used to inject any psr-3 compatible ==logger==.

The third one, any psr-16 (simplecache) compatible ==cache==.   
  
  
```php
<?php
use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\VideoInfoReader;

$infoReader = new VideoInfoReader(
    // @param FFMpegConfigInterface 
    new FFProbeConfig(
        // (?string) - path to ffprobe binary (default: ffprobe/ffprobe.exe)
        $binary = null,
        // (?float)  - max time in seconds for ffprobe process (null: disable)
        $timeout = null, 
        // (?float)  - max idle time in seconds for ffprobe process
        $idleTimeout = null, 
        // (array)   - additional environment variables if needed
        $env = []                           
    ),
    // @param \Psr\Log\LoggerInterface - Default to `\Psr\Log\NullLogger`.     
    $logger = null,
    // @param \Psr\SimpleCache\CacheInterface
    $cache = null   
);

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // Possibly wrong media, see below for exceptions
    // details
}

```

??? tip "Tip: initialize in a container (psr-11)" 
    It's a good idea to register services in a container. 
    Depending on available framework integrations, you may have a look to the [`Video\VideoInfoReaderFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoInfoReaderFactory.php)
    and/or [`FFProbeConfigFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFProbeConfigFactory.php) to get an example based on a psr-11 compatible container.
    See also the provided default [configuration](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file.


### Usage

#### Getting general info 

```php
<?php

use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\Info\StreamTypeInterface;

$infoReader = new VideoInfoReader(new FFProbeConfig());

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // Possibly wrong media, see below for exceptions
    // details
}
 

// Total duration
$duration = $info->getDuration();
$filesize = $info->getFileSize();

// i.e 'mov,mp4,m4a,3gp,3g2,mj2'
$format   = $info->getFormatName();

// Streams (will be detailled below)

$info->getVideoStreams();
$info->getAudioStreams();
$info->getSubtitleStreams();

```

#### Get video stream.

Get video streams information. (generally one, i.e mkv containers can have multiple)

```php
<?php

use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\Exception\NoStreamException;


$infoReader = new VideoInfoReader(new FFProbeConfig());

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // Possibly wrong media, see below for exceptions
    // details
}
 
$videoStreams = $info->getVideoStreams();

// Option 1: The iterable way

foreach($videoStreams as $vStream) {
    // ...
}

// Option 2: Take the first

try {    
    $video = $videoStreams->getFirst();
} catch (NoStreamException $e) {
    // No video stream present
}
    
$video->getCodecName(); // vp9
$video->getFps($decimals=0); // i.e: 24
$video->getCodecTagString();
$video->getFps($roundedDecimals=null); 
$video->getNbFrames();
$video->getHeight();
$video->getWidth();
$video->getDuration();
$video->getDurationTs();

$video->getPixFmt();
$video->getNbFrames();
$video->getTimeBase();
$video->getBitRate();
$video->getTags();
$video->getDisplayAspectRatio();
$video->getSampleAspectRatio();
$video->getCodedWidth();
$video->getCodedHeight();
$video->getRFrameRate();

$aspectRatio = $video->getAspectRatio();
if ($aspectRatio !== null) {
    $aspectRatio->getString(); // '16:9'
    $aspectRatio->getX(); // float
    $aspectRatio->getY(); // float
}

```

#### Get audio streams

```php
<?php

use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\Exception\NoStreamException;

$infoReader = new VideoInfoReader(new FFProbeConfig());

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // Possibly wrong media, see below for exceptions
    // details
}
 
$audioStreams = $info->getAudioStreams();

// Option 1: The iterable way

foreach($audioStreams as $aStream) {
    // ...
}

// Option 2: take the first one

try {
    $audio = $audioStreams->getFirst();
} catch (NoStreamException $e) {
    // Nothing marked as audio
}

$audio->getCodecName(); // aac
$audio->getCodecTagString();
$audio->getDuration();
$audio->getDurationTs();

$audio->getTimeBase();
$audio->getBitRate();
$audio->getTags();

```

#### Get subtitle info

```php
<?php

use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\Exception\NoStreamException;

$infoReader = new VideoInfoReader(new FFProbeConfig());

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // Possibly wrong media, see below for exceptions
    // details
}


// For subtitle streams

$subtitleStreams = $info->getSubtitleStreams();

// Option 1: The iterable way

foreach($subtitleStreams as $sStream) {
    // ...
}


// Option 2: taking the first one

try {
    $subtitle = $subtitleStreams->getFirst();
} catch (NoStreamException $e) {
    // No subtitle streams
}

$subtitle->getCodecName(); // webvtt


```

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


#### Metadata retrieval


```php
<?php

use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\VideoInfoReader;
use Soluble\MediaTools\Video\Info\StreamTypeInterface;

$infoReader = new VideoInfoReader(new FFProbeConfig());

try {
    $info = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // Possibly wrong media, see below for exceptions
    // details
}


// Metadata as returned by ffprobe

$info->getMetadata();

// By stream

$info->getStreamsMetadataByType(StreamTypeInterface::VIDEO);
$info->getStreamsMetadataByType(StreamTypeInterface::AUDIO);
$info->getStreamsMetadataByType(StreamTypeInterface::SUBTITLE);
$info->getStreamsMetadataByType(StreamTypeInterface::DATA);

$nbStreams      = $info->countStreams();

$nbVideoStreams = $info->countStreams(StreamTypeInterface::VIDEO);
$nbAudioStreams = $info->countStreams(StreamTypeInterface::AUDIO);
$nbSubStreams   = $info->countStreams(StreamTypeInterface::SUBTITLE);


```

#### Metadata example

As returned by ffprobe

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
