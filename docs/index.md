[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, querying, thumbnailing... wraps around [ffmpeg](https://www.ffmpeg.org/) and [ffprobe](https://www.ffmpeg.org/ffprobe.html). 

It likes [PSR](https://www.php-fig.org/psr/) (psr-log, psr-container), tastes php 7.1 in strict mode, tries to fail as early as possible 
with clear exception messages and ensure that substitution is possible when you need to customize 
*(SOLID friendly)*.   

Under the hood, it relies on the battle-tested [symfony/process](https://symfony.com/doc/current/components/process.html), its only dependency.
      
## Status

Not yet 1.0 but what's documented works pretty well ;) Travis runs unit and integration/functional 
tests to ensure a smooth experience. But **this project is young** and would ❤️ to meet new contributors !

## Requirements

A PHP version >= 7.1 and depending on required services: ffmpeg and ffprobe.

> For linux, you can easily download statically compiled binaries [here](https://johnvansickle.com/ffmpeg/), 
> alternatively have a look to the [travis install script](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh) too.



## Project philosophy

???+ Info "Note for developers"
    
    - Mediatools is an [opensource](https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md) PHP 7.1+ library and :heart: pull requests and [contributors](https://github.com/soluble-io/soluble-mediatools/blob/master/CONTRIBUTING.md).     
    
    - Mediatools will preferably use PSR standards. It currently allows injection of 
      any PSR-3 compatible logger and provides integrations for PSR-11 container interface.
      (PSR-7 for video thumbnailing is under consideration).     
    
    - Mediatools likes chainability when possible, but completely forbid the use of fluent interfaces in favour
      of immutable api (like PSR-7).  
    
    - Customization can generally be done easily as the project try to respect subsitution 
      principles as much as possible. In most cases, you can swap implementations as you like.
      
    - Mediatools versions adheres to [semantic versioning](http://semver.org/). 
      No bc-break outside of major version releases and we keep a [changelog](https://github.com/soluble-io/soluble-mediatools/blob/master/CHANGELOG.md).  
      
    - Quality assurance is guaranteed through unit and integration/functional tests, 
      [phpstan](https://github.com/phpstan/phpstan) and some php-cs sniffs. 
      Travis is used as continuous integration server.  
        
    - Mediatools is currently released as a monolithic repository, but once more
      diverse services exists (image optimisation, resizing, subtitles conversion...) the architecture
      can be easily split into multiple repositories without affecting existing projects.
      
    - Note that information and thumbnail services can be used in realtime but conversions should be 
      used with a job queue. Conversions are heavy.    


## Alternative(s)

- https://github.com/PHP-FFMpeg/PHP-FFMpeg


