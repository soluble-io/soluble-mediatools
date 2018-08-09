hero: Video information/query service
path: blob/master/src
source: Video/VideoInfoReader.php

### Overview

The ==Video\VideoInfoReader== acts as a wrapper over ffprobe and return information about a video file.

It relies on the [symfony/process](https://symfony.com/doc/current/components/process.html) 
component and attempt to make debugging easier with clean exceptions. You can also inject any psr-3 compatible 
logger if you don't want to log issues by yourself.    
  
```php
<?php
use Soluble\MediaTools\Video\Config\FFProbeConfig;
use Soluble\MediaTools\Video\Exception\InfoReaderExceptionInterface;
use Soluble\MediaTools\Video\VideoInfoReader;

$infoReader = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));

try {
    $videoInfo = $infoReader->getInfo('/path/video.mp4');
} catch (InfoReaderExceptionInterface $e) {
    // see below for exceptions
}

$duration = $videoInfo->getDuration();
$frames   = $videoInfo->getNbFrames();
$width    = $videoInfo->getWidth();
$height   = $videoInfo->getHeight();

// Or alternatively
['width' => $width, 'height' => $height] = $videoInfo->getDimensions();
       
``` 

### Requirements

You'll need to have ffprobe installed on your system.

### Initialization

The [Video\VideoInfoReader](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoInfoReader.php) 
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
    
    $vis->getInfo('/path/video.mov');
    
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

