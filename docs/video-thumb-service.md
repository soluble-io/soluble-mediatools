hero: Video thumb service
path: blob/master/src
source: Video/ThumbService.php

### Overview

The ==Video\ThumbService== acts as a wrapper over ffmpeg and
deal with video thumbnail creation. It relies on the [symfony/process](https://symfony.com/doc/current/components/process.html) 
component, exposes an immutable api for thumbnailing parameters and attempt to make debugging
easier with clean exceptions. You can also inject any psr-3 compatible logger if you don't want 
to log issues by yourself.    
    

```php hl_lines="8 9 12 13 14 15 16"
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\ConversionExceptionInterface;
use Soluble\MediaTools\Video\{ThumbService, ThumbParams, SeekTime};

$vts = new ThumbService(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new ThumbParams())
    ->withTime(1.25);
    
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



### Requirements

You'll need to have ffmpeg installed on your system.

### Initialization

The [Video\ThumbService](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ThumbService.php)
requires an [`FFMpegConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfig.php) object as first parameter. 
This is where you set the location of the ffmpeg binary, the number of threads you allow for conversions
and the various timeouts if needed. The second parameter can be used to inject any psr-3 compatible ==logger==. 

```php
<?php
use Soluble\MediaTools\Video\Config\{FFMpegConfig, FFMpegConfigInterface};
use Soluble\MediaTools\Video\ThumbService;

$vcs = new ThumbService(    
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
    Depending on available framework integrations, you may have a look to the 
    [`Video\ThumbServiceFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ThumbServiceFactory.php)
    and/or [`FFMpegConfigFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfigFactory.php) to get an example based on a psr-11 compatible container.
    See also the provided default [configuration](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file.
               
### Usage

#### Thumbnailing
 
The `Video\ThumbService` offers a quick and simple `makeThumbnail()` method in which you specify the input/output files
as well as the thumb params. 

```php
<?php

use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\{ThumbService, ThumbParams, SeekTime};

$vts = new ThumbService(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new ThumbParams())
    ->withSeekTime(new SeekTime(1.25));

$vts->makeThumbnail(
    '/path/inputFile.mov', 
    '/path/outputFile.jpg',
    $params 
);    
       
``` 

*The `makeThumbnail()` method will automatically set the process timeouts, logger... as specified during 
service initialization.* 

??? question "What if I need more control over the process ? (advanced usage)"
    You can use the `Video\ThumbService::getSymfonyProcess(string $inputFile, string $outputFile, ConversionParamsInterface $convertParams, ?ProcessParamsInterface $processParams = null): Process` 
    to get more control on the conversion process. 
    ```php 
    <?php
    $process = $thumbService->getSymfonyProcess(
        '/path/inputFile.mov', 
        '/path/outputFile.jpg', 
        (new ThumbParams())
            ->withTime(1.25)
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
 
The [`Video\ThumbParams`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ThumbParams.php) 
exposes an immutable api that attempt to mimic ffmpeg params.  
   
```php
<?php
use Soluble\MediaTools\Video\ThumbParams;

$params = (new ThumbParams())
    ->withQualityScale(2)
    ->withTime(12.23);
    // alternatively
    //->withSeekTime(SeekTime::createFromHMS('0:00:12.23'));
```

??? question "Immutable api, what does it change for me ? (vs fluent)"
    ThumbParams exposes an ==immutable== style api *(`->withXXX()`, like PSR-7 for example)*.
    It means that the original object is never touched, the `withXXX()` methods will return a newly 
    created object. 
    
    Please be aware of it especially if you're used to ~==fluent==~ interfaces as both expose 
    chainable methods... your primary reflexes might cause pain: 
    
            
    ```php hl_lines="6 9"
    <?php
    $params = (new ThumbParams());
    
    $newParams = $params->withSeekTime(new SeekTime(1.1212));
    
    // $params does not contain SeekTime (incorrect usage)
    $vts->convert('i.mov', 'output.jpg', $params);
    
    // $newParams contains SeekTime (correct)
    $vts->convert('i.mov', 'output.jpg', $newParams);     
    
    ```

Here's a list of categorized built-in methods you can use. See the ffmpeg doc for more information. 


- Time related:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withTime(float)`             | -ss ◌                  | 61.123     | Set the time in seconds, decimals are considered milliseconds |   |
| `withSeekTime(SeekTime)`      | -ss ◌                  | [`SeekTime::createFromHms('0:00:01.9')`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/SeekTime.php) |   |

- Quality options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                      |
| ----------------------------- | ---------------------- | ---------- | ---------------------------- |
| `withQualityScale(int)`  | -qscale:v ◌            | 5             |  |


- General process options:

| Method                        | FFmpeg arg(s)          | Example(s) | Note(s)                              |
| ----------------------------- | ---------------------- | ---------- | ------------------------------------ |
| `withOutputFormat(string)`    | -format ◌              | jpeg,png…  | file extension *(if not provided)*   |
| `withOverwrite()`             | -y                     |            | by default. overwrite if file exists |
| `withNoOverwrite()`           |                        |            | throw exception if output exists     |

- Other methods:

| Method                            | Note(s)                              |
| --------------------------------- | ------------------------------------ | 
| `withBuiltInParam(string, mixed)` | With any supported built-in param, see [constants](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/ThumbParamsInterface.php).  | 
| `withoutParam(string)`            | Without the specified parameter. |
| `getParam(string $param): mixed`  | Return the param calue or throw UnsetParamExeption if not set.      |
| `hasParam(string $param): bool`   | Whether the param has been set.  |
| `toArray(): array`                | Return the object as array.      |




#### Filters

Video filters can be set to the ConversionParams through the `withVideoFilter(VideoFilterInterface $videoFilter)` method:

```php
<?php
use Soluble\MediaTools\Video\ThumbParams;
use Soluble\MediaTools\Video\Filter;

$params = (new ThumbParams())
    ->withTime(1.123)
    ->withVideoFilter(new Filter\VideoFilterChain([
        new Filter\YadifVideoFilter(),
        new Filter\NlmeansVideoFilter()
    ])); 

```

Currently there's only few built-in filters available:

| Filter                   |Note(s)                               |
| ------------------------ | ------------------------------------ | 
| [`YadifVideoFilter`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/YadifVideoFilter.php)       | Deinterlacer  | 
| [`Hqdn3DVideoFilter`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/Hqdn3DVideoFilter.php)       | Basic denoiser  | 
| [`NlmeansFilter`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/NlmeansVideoFilter.php)       | Very slow but good denoiser  | 
 
> But it's quite easy to add yours, simply implements the [FFMpegVideoFilterInterface](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/Type/FFMpegVideoFilterInterface.php).
> We :heart: pull request, so don't forget to share :)


#### Exceptions

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
    
    // The following 3 exceptions are linked to process
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
