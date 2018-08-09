# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 0.7.0 (2018-08-XX)

**WARNING THIS IS BC-BREAK RELEASE**

### CHANGED

#### Conversion 

- [BC-BREAK] Renamed `ConversionService` to `VideoConverter`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)
- [BC-BREAK] Renamed `ConversionServiceInterface` to `VideoConverterInterface`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)
- [BC-BREAK] Renamed `ConversionServiceFactory` to `VideoConverterFactory`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)

#### VideoQuery

- [BC-BREAK] Renamed `InfoService` to `VideoQuery`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)
- [BC-BREAK] Renamed `InfoServiceInterface` to `VideoQueryInterface`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)
- [BC-BREAK] Renamed `Info` to `VideoInfo`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2) 
- [BC-BREAK] Renamed `InfoInterface` to `VideoInfoInterface`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2) 


#### Thumbnail

- 

### Added

- Requirement of `symfony/polyfill-mbstring` to allow install on system without mbstring extension. 

## 0.6.3 (2018-08-08)

### Added

- Missing requirement of 'ext-mbstring' in composer.json. 

### Fixed

- Minor null file (`PlatformNullFile`) test when not autodetected. 

## 0.6.2 (2018-08-07)

### Added

- Convenience method: `ThumbParams::withTime(float $time)` (will set to SeekTime).
- New methods for video info : `Info::getWidth()`, `Info:getHeight()`

### Updated

- A lot of missing documentation

### Fixed

- `ConversionParams::getParam()` now throws an `UnsetParamException` when the parameter was not set.
- `ThumbParams::getParam()` now throws an `UnsetParamException` when the parameter was not set.

## 0.6.1 (2018-07-12)

### Added

- New method for video conversion params `ConvertParams::withoutParam(string $paramName)`.
- New method for video thumbnail params `ThumbParams::withoutParam(string $paramName)`. 

### Changed 

- Internal `AdapterInterface` becomes `ConverterAdapterInterface`.
- Internal `FFMpegConverterAdapter` becomes `FFMpegAdapter`.

### Improved

- Documentation site !!!

### Fixed

- Fixed exception `MissingTimeException`, that was missing ;)


## 0.6.0 (2018-07-11)

### Changed

- [BC-Break] `ThumbServiceInterface::makeThumbnail()` now requires `ThumbParamsInterface`.

### Added

- Support for psr/log in video conversion, thumbnail and info services.  
- Added `VideoFilterChain::__construct(array $filters = [])`
- Added `VideoFilterChain::addFilters(array $filters = [])`

### Improved

- Separation of concern for `ProcessParamInterface`

## 0.5.0 (2018-07-10) 

### Changed

- [BC-Break] Moved `[Conversion|Thumb|Detection|Info]Service` one level up in `Video\[Conversion|Thumb|Detection|Info]Service`.
  As well `VideoConversionParams` becomes `Video\ConversionParams`. Search/replace should be sufficient.
  *(This change makes possible a future split of this repo (audio-video-subtitles...) without
  sacrificing BC)*.   
- [BC-Break] `ProcessTimeOutException` renamed to `ProcessTimedOutException` for coherence.
- [BC-Break] Moved `Config\*` to `Video\Config\*`, update FFMpegConfig, FFProbeConfig, ConfigProvider locations.

> The following changes concerns internal classes (less possible bc-break):

- Possible bc-break moved base exception into `MediaTools\Common` namespace.
- Possible bc-break some internal util classes moved into `MediaTools\Common` namespace.
- Possible bc-break `PlatformNullFile` moved into `MediaTools\Common\IO` namespace.

### Improved

- Consumption of `ConversionParamsInterface` instead of concrete implementation in `convert()`.

### Added

- Possibility to set timeout per conversion: see `ProcessParamsInterface` in `convert()` or `makeThumbnail()`
- Q&A Testing timed-out behaviour (functional tests working)


## 0.4.0 (2018-07-09)  

### Changed

- [BC-Break] Renamed params in global [configuration file](config/soluble-mediatools.config.php)
- [BC-Break] Renamed `VideoInfoService::getMediaInfo()` in `VideoInfoService::getInfo()`
- [BC-Break] Renamed `VideoConversionService::getConversionProcess()` in `getSymfonyProcess()`
- Moved internal class `VideoInfo` in `Video\Info` namespace.
- `VideoConversionService` set `withOverwrite()` by default.

### Added

- `ConversionParamsInterface::withBuiltInParam()` to set a built-in (supported) param. 
- `ConversionParamsInterface::withNoOverwrite()` to ensure a file is never overwritten

### Improved

- Uniform Exceptions for conversion, thumbnailing and infos (doc coming).
- `VideoInfoServices` now relies on `symfony/process`
- Improved config params separation for `FFProbeConfig` and `FFMpegConfig`.
- Improved customization of config factories `FFProbeConfigFactory` and `FFMpegConfigFactory`
- Improved error reporting using config factories.
- Factories for `FFProbeConfigFactory` and `FFMpegConfigFactory` uses interfaces.
 
### Fixed

- Added missing interface methods in `AdapterInterface`
- Added missing interface methods in `ConversionParamsInterface`

### Removed

- `AbstractProcess` family internal classes, no use exclusively `symfony/process` 

## 0.3.0 (2018-07-08)  

### Changed

- [BC-Break] `Video\ProbeService` renamed to `Video\InfoService`.
- [BC-Break] Concrete services instances moved to top level directory.

### Added

- Support for `withSeekStart` and `withSeekEnd` methods.
- Support for `withOverwrite`.


## 0.2.0 (2018-07-06) 

### Changed

- [BC-Break] *Complete class re-organisation*, this kind of refactoring will tend
  to be avoided in subsequent releases.

### Added

- Configuration: Idle and regular timeouts for conversions.
- Q&A: Travis tests, unit and functional/integration tests (a start).

### Removed 

- Interlacing detection in `VideoConvert` service
