## Installation

### Binaries 

> This library relies on FFMpeg binaries 
>
> - **[ffmpeg](https://ffmpeg.org/ffmpeg.html)** is required by `VideoConverter`, `VideoDetectionService` and `VideoThumbGenerator`.
> - **[ffprobe](https://ffmpeg.org/ffprobe.html)** is required by `VideoInfoReader`.
>  
> Statically [compiled binaries](https://ffmpeg.org/download.html) exists in case your OS does not provide them.
>
> *Tip: For integration tests see our [ffmpeg travis install](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh) script*. 


## Configuration

### PSR-11/container way 

> Mediatools is PSR-11/container friendly and provides some ready to use factories.

#### Create a config file  

```php
<?php 
return [
    'soluble-mediatools' => [

        'ffmpeg.binary'         => 'ffmpeg',   // Or a complete path /opt/local/ffmpeg/bin/ffmpeg
        //'ffmpeg.threads'        => null,       // <null>: single thread; <0>: number of cores, <1+>: number of threads
        //'ffmpeg.timeout'        => null,       // <null>: no timeout, <number>: number of seconds before timing-out
        //'ffmpeg.idle_timeout'   => null,       // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        //'ffmpeg.env'            => [],         // An array of additional env vars to set when running the ffmpeg conversion process


        'ffprobe.binary'        => 'ffprobe',  // Or a complete path /opt/local/ffmpeg/bin/ffprobe
        //'ffprobe.timeout'       => null,       // <null>: no timeout, <number>: number of seconds before timing-out
        //'ffprobe.idle_timeout'  => null,       // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        //'ffprobe.env'           => [],         // An array of additional env vars to set when running the ffprobe
    ],
];
```

> Tip: Have a look to the [config/soluble-mediatools.config.php](https://github.com/soluble-io/soluble-mediatools/blob/master/config/soluble-mediatools.config.php) file
> for most up-to-date info about defaults.

#### Registration 
 
Require the config file and feed your container (example with zend-servicemanager)  
 
```php
<?php 

use Zend\ServiceManager\ServiceManager;
use Soluble\MediaTools\Video\Config\ConfigProvider;

$config = require('/path/config/soluble-mediatools.config.php');

// Service manager
$container = new ServiceManager(
                array_merge([
                    // In Zend\ServiceManager configuration will be set
                    // in 'services'.'config'. 
                    'services' => [
                        'config' => $config
                    ]],
                    // Here the factories
                    (new ConfigProvider())->getDependencies()
             ));

```

> Tip: Have a look to the [ConfigProvider](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Config/ConfigProvider.php) class
> to get some ideas about registered factories / aliases


### Framework(s) integration

> No framework integration have been done yet... Open a P/R or send us a link.
>
> - [ ] zend-expressive (wip) 
> - [ ] Laravel (todo)
> - [ ] Symfony (todo)
>

