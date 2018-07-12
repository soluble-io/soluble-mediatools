hero: Video detection service
path: blob/master/src
source: Video/DetectionService.php

The ==Video\DetectionService== will analyze a video stream and currently
detects/infer interlaced videos. It acts as a wrapper over ffmpeg.   

### At a glance

```php
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\DetectionExceptionInterface;
use Soluble\MediaTools\Video\DetectionService;

$vds = new DetectionService(new FFMpegConfig('/path/to/ffmpeg'));

    
try {    
    $interlaceGuess = $vds->detectInterlacement(
        '/path/input.mov',
        // Optional:
        //   $maxFramesToAnalyze, default: 1000
        $maxFramesToAnalyze = 200
    );
    
} catch(DetectionExceptionInterface $e) {
    // See chapter about exception !!!    
}

$interlaced = $interlaceGuess->isInterlaced(
    // Optional: 
    //  $threshold, default 0.25 (if >=25% interlaced frames, then true) 
    0.25
);

``` 

### Initialize

```php
<?php
use Soluble\MediaTools\Video\Config\{FFMpegConfig, FFMpegConfigInterface};
use Soluble\MediaTools\Video\DetectionService;

$vcs = new DetectionService(    
    // FFMpegConfigInterface
    new FFMpegConfig(
        $binary = 'ffmpeg',  // (?string) - path to ffmpeg binary 
        $threads = null,     // (?int)    - ffmpeg default threads (null: single-thread)
        $timeout = null,     // (?float)  - max time in seconds for ffmpeg process (null: disable) 
        $idleTimeout = null, // (?float)  - max idle time in seconds for ffmpeg process
        $env = []            // (array)   - additional environment variables               
    ),
    // ?\Psr\Log\LoggerInterface - Default to `\Psr\Log\NullLogger`.     
    $logger = null   
);
```


### Exception


```php
<?php
use Soluble\MediaTools\Video\DetectionService;
use Soluble\MediaTools\Video\Exception as VE;

/** @var DetectionService $vds */
     
try {
    $interlaceGuess = $vds->detectInterlacement(
        '/path/input.mov',
    );

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

todo full example detection + deint + denoise
