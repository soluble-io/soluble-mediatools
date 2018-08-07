[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, transmuxing, thumbnailing... wraps around ffmpeg and ffprobe. 

???+ Info "Note for developers"
    
    - Mediatools is opensource PHP 7.1+ project and :heart: pull requests and contributors.     
    
    - Mediatools will preferably use PSR standards. It currently allows injection of 
      any PSR-3 compatible logger and provides integrations for PSR-11 container interface.
      (PSR-7 for video thumbnailing is under consideration).     
    
    - Mediatools likes chainability when possible, but completely forbid the use of fluent interfaces in favour
      of immutable api (like PSR-7).  
    
    - Customization can generally be done easily as the project try to respect subsitution 
      principles as much as possible. In most cases, you can swap implementations as you like.
      
    - Mediatools versions adheres to [semantic versioning](http://semver.org/). 
      No bc-break outside of major version releases and we keep a [changelog](https://github.com/soluble-io/soluble-mediatools/blob/master/CHANGELOG.md).  
      
    - Quality assurance is guaranteed through unit and integration/functional tests, phpstan and
      and some php-cs sniffs. Travis is used as continuous integration server.  
        
    - The soluble-mediatools is currently released as a monolithic repository, but once more
      diverse services exists (image optimisation, resizing, subtitles conversion...) the architecture
      can be easily split into multiple repositories without affecting existing projects.
      
    - Information and thumbnail services can be used in realtime but conversions should be 
      used with a job queue.     

### Requirements

A PHP version >= 7.1 and depending on required services: ffmpeg and ffprobe.

### Installation

Installation in your project

```bash
$ composer require soluble/mediatools
``` 

### Status

Not yet 1.0 but what is documented works well ;)

## Features

> Video services:

- [X] `Video\ConversionService`.
  - [X] Transcoding, transmuxing, compression (audio/video)     
  - [X] Video Filters (Chainable filters)      
      - [X] Deinterlace (`YadifVideoFilter`)
      - [X] Denoise (`Hqdn3dVideoFilter`, `NlmeansVideoFilter`)
      - [ ] Video scaling (todo)
  - [X] Video clipping (seekstart - seekend)                  
  - [ ] Option to enable multipass transcoding (todo)
- [X] `Video\InfoService` 
  - [X] Basic information like duration, frames....
- [X] `Video\ThumbService`
  - [X] Basic thumbnail creation
- [X] `Video\DetectionService`.
  - [X] Infer/detect [interlaced](https://en.wikipedia.org/wiki/Interlaced_video) *(BFF, TFF)* vs [progressive](https://en.wikipedia.org/wiki/Progressive_scan) encoded videos.  




