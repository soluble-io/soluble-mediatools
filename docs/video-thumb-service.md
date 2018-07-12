hero: Video thumb service
path: blob/master/src
source: Video/ThumbService.php

The ==Video\ThumbService== acts as a wrapper over ffmpeg and
currently allows creation of video thumbnails.  

### At a glance

```php
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\ConversionExceptionInterface;
use Soluble\MediaTools\Video\{ThumbService, ThumbParams, SeekTime};

$vts = new ThumbService(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new ThumbParams())
    ->withSeekTime(new SeekTime(1.25));
    
try {    
    $vts->makeThumbnail(
        '/path/inputFile.mov', 
        '/path/outputFile.jpg', 
        $params
    );    
} catch(ConversionExceptionInterface $e) {
    // See chapter about exception !!!    
}
       
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



### Params

```php
<?php
use Soluble\MediaTools\Video\{ThumbParams, SeekTime, Filter};

$params = (new ThumbParams())
    ->withQualityScale(2)
    ->withSeekTime(SeekTime::createFromHMS('0:00:05.123'))
    ->withVideoFilter(new Filter\VideoFilterChain([
        new Filter\EmptyVideoFilter(),
        new Filter\YadifVideoFilter()
    ])); 

```
 
???+ warning
    ==ThumbParams== exposes an ==immutable :heart:== style api *(`->withXXX()`, just like PSR-7 and others)*.
    It means that the original params are never touched, the `withXXX()` methods always return a copy. 
    Please be aware of it especially if you're used to ~==fluent==~ interfaces as both exposes chainable methods
    and look similar... your primary reflexes might cause pain: 
    
            
    ```php 
    <?php
    $params = (new ThumbParams());
    
    $newParams = $params->withSomething('cool')
                        ->withSomethingElse('cool');
    
    // The two next lines won't use the same params !!!
    $vts->convert('i.mov', 'output', $params); 
    $vts->convert('i.mov', 'output', $newParams);     
    
    ```


### Video filters

todo

### Exception

You can safely catch exceptions with the generic `Soluble\MediaTools\VideoException\ConversionExceptionInterface`,
alternatively you can also :


```php
<?php
use Soluble\MediaTools\Video\{ThumbService, ThumbParams};
use Soluble\MediaTools\Video\Exception as VE;

/** @var ThumbService $vts */
$params = (new ThumbParams());     
try {

    $vts->makeThumbnail('i.mov', 'out.jpg', $params);    
    
} catch(VE\MissingInputFileException $e) {
    
    // 'i.mov does not exists
    
    echo $e->getMessage();

// All exception below implements Ve\ConversionExceptionInterface
// It's possible to get them all in once
    
} catch(VE\MissingTimeException $e) {
    
    // Missing required time
    
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

todo

