# soluble-mediatools  

[![PHP Version](https://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/mediatools)
[![Build Status](https://travis-ci.org/soluble-io/soluble-mediatools.svg?branch=master)](https://travis-ci.org/soluble-io/soluble-mediatools)
[![codecov](https://codecov.io/gh/soluble-io/soluble-mediatools/branch/master/graph/badge.svg)](https://codecov.io/gh/soluble-io/soluble-mediatools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/soluble-io/soluble-mediatools/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/mediatools/v/stable.svg)](https://packagist.org/packages/soluble/mediatools)
[![Total Downloads](https://poser.pugx.org/soluble/mediatools/downloads.png)](https://packagist.org/packages/soluble/mediatools)
[![License](https://poser.pugx.org/soluble/mediatools/license.png)](https://packagist.org/packages/soluble/mediatools)

Toolbox for video conversions, transcoding, transmuxing, thumbnailing... wraps around ffmpeg and ffprobe. 

## Status

WIP at speed of light... documentation in progress: 

**https://soluble-io.github.io/soluble-mediatools/**

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


## Requirements

- PHP 7.1+
- FFmpeg 3.4+, 4.0+, see [install](#binaries). 
 
  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

