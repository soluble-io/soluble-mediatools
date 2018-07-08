# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 0.4.0 (2018-??-??)  

### Added

- `ConversionParamsInterface::withBuiltInParam()` to set a built-in (supported) param. 
- `ConversionParamsInterface::withNoOverwrite()` to ensure a file is never overwritten

### Changed

- `VideoConversionParams::withOverwrite()` is interpreted by default (no need to specify)

### Fixed

- Added missing interface methods in `AdapterInterface`
- Added missing interface methods in `ConversionParamsInterface`


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
