# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 0.4.0 (2018-??-??)  

### Changed

- [BC-Break] Renamed params in global [configuration file](./config/soluble-mediatools.global.php)
- [BC-Break] Renamed `VideoInfoService::getMediaInfo()` in `VideoInfoService::getInfo()`
- [BC-Break] Renamed `VideoConversionService::getConversionProcess()` in `getSymfonyProcess()`
- Moved internal class `VideoInfo` in `Video\Info` namespace.
- `VideoConversionService` set `withOverwrite()` by default.

### Added

- `ConversionParamsInterface::withBuiltInParam()` to set a built-in (supported) param. 
- `ConversionParamsInterface::withNoOverwrite()` to ensure a file is never overwritten

### Improved

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
