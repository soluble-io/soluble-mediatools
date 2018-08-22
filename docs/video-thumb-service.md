path: blob/master/src
source: Video/VideoThumbGenerator.php

The ==VideoThumbGenerator service== acts as a wrapper over ffmpeg and
deal with video thumbnail creation. 

It exposes an immutable api for thumbnail generation parameters and attempt to make 
debugging easier with clean exceptions. You can also inject any psr-3 compatible 
logger if you don't want to log issues by yourself.
    

```php hl_lines="8 9 12 13 14 15 16"
<?php
use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\Exception\ConverterExceptionInterface;
use Soluble\MediaTools\Video\{VideoThumbGenerator, VideoThumbParams, SeekTime};

$generator = new VideoThumbGenerator(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new VideoThumbParams())
    ->withTime(1.25);
    
try {    
    $generator->makeThumbnail(
        '/path/inputFile.mov', 
        '/path/outputFile.jpg', 
        $params
    );    
} catch(ConverterExceptionInterface $e) {
    // See chapter about exception !!!    
}
       
``` 

### Requirements

You'll need to have ffmpeg [installed](./install-ffmpeg.md) on your system.

### Initialization

The [VideoThumbGenerator](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoThumbGenerator.php)
requires an [`FFMpegConfig`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfig.php) 
object as first parameter. 
This is where you set the location of the ffmpeg binary, the number of threads you allow for conversions
and the various timeouts if needed. The second parameter can be used to inject any psr-3 compatible ==logger==. 

```php
<?php
use Soluble\MediaTools\Video\Config\{FFMpegConfig, FFMpegConfigInterface};
use Soluble\MediaTools\Video\VideoThumbGenerator;

$converter = new VideoThumbGenerator(    
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
    [`VideoThumbGeneratorFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoThumbGeneratorFactory.php)
    and/or [`FFMpegConfigFactory`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/FFMpegConfigFactory.php) to get an example based on a psr-11 compatible container.
    See also the provided default [configuration](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file.
               
### Usage

#### Thumbnailing
 
Typically you'll use the `VideoThumbGenerator::makeThumbnail()` method in which you specify the input/output files
as well as the thumbnail params. 

```php
<?php

use Soluble\MediaTools\Video\Config\FFMpegConfig;
use Soluble\MediaTools\Video\{VideoThumbGenerator, VideoThumbParams, SeekTime};

$generator = new VideoThumbGenerator(new FFMpegConfig('/path/to/ffmpeg'));

$params = (new VideoThumbParams())
    ->withSeekTime(new SeekTime(1.25));

$generator->makeThumbnail(
    '/path/inputFile.mov', 
    '/path/outputFile.jpg',
    $params 
);    
       
``` 

*The `makeThumbnail()` method will automatically set the process timeouts, logger... as specified during 
service initialization.* 

??? question "What if I need more control over the process ? (advanced usage)"
    You can use the `Video\VideoThumbGenerator::getSymfonyProcess(string $inputFile, string $outputFile, VideoConvertParamsInterface $convertParams, ?ProcessParamsInterface $processParams = null): Process` 
    to get more control on the conversion process. 
    ```php 
    <?php
    $process = $thumbService->getSymfonyProcess(
        '/path/inputFile.mov', 
        '/path/outputFile.jpg', 
        (new VideoThumbParams())
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
 
The [`Video\VideoThumbParams`](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoThumbParams.php) 
exposes an immutable api that attempt to mimic ffmpeg params.  
   
```php
<?php
use Soluble\MediaTools\Video\VideoThumbParams;

$params = (new VideoThumbParams())
    ->withQualityScale(2)
    ->withTime(12.23);
    // alternatively
    //->withSeekTime(SeekTime::createFromHMS('0:00:12.23'));
```

??? question "Immutable api, what does it change for me ? (vs fluent)"
    VideoThumbParams exposes an ==immutable== style api *(`->withXXX()`, like PSR-7 for example)*.
    It means that the original object is never touched, the `withXXX()` methods will return a newly 
    created object. 
    
    Please be aware of it especially if you're used to ~==fluent==~ interfaces as both expose 
    chainable methods... your primary reflexes might cause pain: 
    
            
    ```php hl_lines="6 9"
    <?php
    $params = (new VideoThumbParams());
    
    $newParams = $params->withSeekTime(new SeekTime(1.1212));
    
    // $params does not contain SeekTime (incorrect usage)
    $generator->convert('i.mov', 'output.jpg', $params);
    
    // $newParams contains SeekTime (correct)
    $generator->convert('i.mov', 'output.jpg', $newParams);     
    
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
| `withBuiltInParam(string, mixed)` | With any supported built-in param, see [constants](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/VideoThumbParamsInterface.php).  | 
| `withoutParam(string)`            | Without the specified parameter. |
| `getParam(string $param): mixed`  | Return the param calue or throw UnsetParamExeption if not set.      |
| `hasParam(string $param): bool`   | Whether the param has been set.  |
| `toArray(): array`                | Return the object as array.      |




#### Filters

Video filters can be set to the VideoThumbParams through the `->withVideoFilter(VideoFilterInterface $videoFilter)` method:

```php
<?php
use Soluble\MediaTools\Video\Filter;

$params = (new VideoConvertParams())
    ->withVideoFilter(
        new Filter\VideoFilterChain([
            // A scaling filter
            new Filter\ScaleFilter(800, 600),
            // Deint filter
            new Filter\YadifVideoFilter(),
            // Denoise (slow but best denoiser, ok for thumbs)
            new Filter\NlmeansVideoFilter()
        ])
    );

```

See the **[complete video filters doc here](./video-filters.md)** 


#### Exceptions

You can safely catch exceptions with the generic `Soluble\MediaTools\VideoException\ConverterExceptionInterface`,
alternatively you can also :


```php
<?php
use Soluble\MediaTools\Video\{VideoThumbGenerator, VideoThumbParams};
use Soluble\MediaTools\Video\Exception as VE;

/** @var VideoThumbGenerator $generator */
$params = (new VideoThumbParams());     
try {

    $generator->makeThumbnail('i.mov', 'out.jpg', $params);    
    
} catch(VE\MissingInputFileException $e) {
    
    // 'i.mov does not exists
    
    echo $e->getMessage();

// All exception below implements Ve\ConverterExceptionInterface
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
    
} catch(VE\ConverterExceptionInterface $e) {
    
    // Other exceptions can be
    //
    // - VE\RuntimeException
    // - VE\InvalidParamException (should not happen)
}
       
``` 
