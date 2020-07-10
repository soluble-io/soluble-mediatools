# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.1.1 (2020-07-10)

### Fixed

- [Minor] phpdoc covariance issue in NullCache, in [#21](https://github.com/soluble-io/soluble-mediatools/pull/21)
- [Minor] process-exception possible covariance issue when extending, in [#21](https://github.com/soluble-io/soluble-mediatools/pull/21)

### Dev

- [Q&A] Updated phpstan/psalm and few dev deps, in [#21](https://github.com/soluble-io/soluble-mediatools/pull/21)
- [Q&A] Allow phpunit v9 and remove deprecations, in [#22](https://github.com/soluble-io/soluble-mediatools/pull/22)

## 2.1.0 (2019-12-21)

### Added

- Support for symfony/process ^5.0

### Dev

- [ci] added PHP 7.4 to travis
- [minor] phpdoc, phpstan ^0.12 generics annotations


## 2.0.6 (2019-09-19)

### Bugs

- [minor] Psalm, default error code for ProcessException enforced to 1.
- [minor] Psalm, VideoStream unnecessary test for null duration.


## 2.0.5 (2019-06-03)

### Bugs

- `VideoThumbParams` throws `InvalidParamException` instead of InvalidArgumentException
- `VideoConvertParams` throws `InvalidParamException` instead of InvalidArgumentException

## 2.0.4 (2019-04-11)

### Bugs

- Fixed exception message for VideoInfo when file does not exists.
- Minor: `ProcessException` supports unknown exit error texts.

## 2.0.3 (2019-03-28)

### Improved

- Cache key generation includes `__METHOD__` in `VideoInfoReader`.

## 2.0.2 (2019-03-19)

### Improved

- Stream collections now properly document 'NoStreamException'.
- Improved documentation for VideoInfoReader.

## 2.0.1 (2019-03-15)

### Improved

- Fail earlier is input file is a directory for all services (not a regular file)

### Added

- `ConfigProvider::getBaseDir()` to allow base class extend.

## 2.0.0 (2019-03-14)

### Changes

- Missing `final` keywords for classes that are not supposed to be extended
  to enforce possible bc issues later. Upgrade to 2.0 is fine if you're not extending
  base classes.


## 1.0.1 (2019-03-13)

### Fixes

- Minor, aspect ratio __toString(), return with separator from constructor instead of default.
- Minor, use alias for psr interfaces in logger and cache to prevent accidental collisions.

## 1.0.0 (2019-03-12)

### Improved

- Improved exception and logging messages for VideoInfoReader, VideoThumbGenerator and VideoConverter.

### Added

- Support for AspectRatio in VideoStreams: `VideoInfo::getVideoStreams()->getFirst()->getAspectRatio()`
- ConfigProvider includes `Video\Logger\LoggerInterface` and `Video\Cache\CacheInterface`.

### Removed/BC

- `VideoInfo::getVideoBitRate(): int` -> Use `VideoInfo::getVideoStreams()->getFirst()->getBitRate()` instead.
- `VideoInfo::getAudioBitRate(): int` -> Use `VideoInfo::getAudioStreams()->getFirst()->getBitRate()` instead.
- `VideoInfo::getVideoCodecName(): ?string` -> Use `VideoInfo::getVideoStreams()->getFirst()->getCodecName()` instead.
- `VideoInfo::getAudioCodecName(): ?string` -> Use `VideoInfo::getAudioStreams()->getFirst()->getCodecName()` instead.

- Removed undocumented `Video\Config\LoggerConfigInterface` from container specs, replaced by standard factories `Video\Logger\LoggerInterface::class`;


## 0.9.7 (2019-03-04)

### Improvements

- Ensure input files are not empty (instead of relying on ffmpeg cli failure - speedup)

### Added

- `VideoInfoReader` now accepts a *psr-16 / simple-cache* implementation in the constructor
- `VideoInfoReader::getInfo($file, CacheInterface $cache=null)` to allow using a specific *psr-16* cache implementaton.
- Specific `InvalidFFProbeJsonException` in `VideoInfoReader::getInfo()` to improve debug.
- `VideoStream::getFps(?int $decimals=null)` to get the stream framerate.

## 0.9.6 (2019-02-26)

### Added

- `VideoStream::getFps(?int $decimals=null)` to get the stream framerate.

## 0.9.5 (2019-02-26)

### Fixed

- Do not require 'duration', 'duration_ts' and 'bitrate' for SubtitleStreamInterface


## 0.9.4 (2019-02-26)

### Added

- Added support for Subtitle streams in VideoInfo.

## 0.9.3 (2019-02-23)

### Added

- VideoInfo now supports getting multiple audio and video streams.
- Added `MissingFFMpegBinaryException` and `MissingFFProbeBinaryException` (ProcessFailedException).

### Bugfix

- Missing deprecation in VideoInfoInterface
- Missing new `VideoInfoInterface::getVideoStreams()` and `VideoInfoInterface::getAudioStreams()`
- Some issues with type requirements in VideoInfo

### Improvement

- Removal of webmozart/assert dependency.

## 0.9.2 (2019-02-20)

### Added

- VideoInfo now supports getting multiple audio and video streams.

### Deprecated

- `VideoInfo::getVideoBitRate(): int` -> Use `VideoInfo::getVideoStreams()->getFirst()->getBitRate()` instead.
- `VideoInfo::getAudioBitRate(): int` -> Use `VideoInfo::getAudioStreams()->getFirst()->getBitRate()` instead.
- `VideoInfo::getVideoCodecName(): ?string` -> Use `VideoInfo::getVideoStreams()->getFirst()->getCodecName()` instead.
- `VideoInfo::getAudioCodecName(): ?string` -> Use `VideoInfo::getAudioStreams()->getFirst()->getCodecName()` instead.


## 0.9.1 (2019-01-05)

### Added

- Missing phpdoc typehints for `VideoInfoInterface`

## 0.9.0 (2019-01-05)

### Added

- `VideoInfo::countStreams(): int`
- `VideoInfo::getFileSize(): int`
- `VideoInfo::getVideoBitRate(): int`
- `VideoInfo::getAudioBitRate(): int`
- `VideoInfo::getVideoCodecName(): ?string`
- `VideoInfo::getAudioCodecName(): ?string`
- `VideoInfo::getFormatName(): string`
- `VideoInfo::getMetadata()`
- `VideoInfo::getAudioStreamsMetadata()`
- `VideoInfo::getVideoStreamsMetadata()`
- `VideoInfo::getStreamsMetadataByType(string $streamType)`

### Fixed

- Minor bug when not specifying crf in conversions

### Change

- [Breaking Change] `VideoInfoInterface::getBitrate()` renamed to `VideoInfoInterface::getVideoBitrate()`


## 0.8.7 (2019-01-05)

### Fixed

- Minor fix: `ConverterAdapterInterface` allowed `null` input file.

### Changed

- Minor phpdoc typehints tuning

### Q&A

- Added psalm to Q&A checks

## 0.8.6 (2018-12-27)

### Improvement

- `symfony/process` 3.3 supported in addition to 3.4, 4.0, 4.1 and 4.2 ! (^3.3 || ^4.0)
- Removed dead code in ProcessFactory

## 0.8.5 (2018-12-20)

### Improvement

- `symfony/process` 3.4 allowed and tested ! (^3.4 || ^4.0)

## 0.8.4 (2018-12-17)

### Bugfix

- [Major] Recently introduced for `symfony/process 4.2` broke older versions.

### Improvement

- Now relies on arguments escaping offered by `symfony/process`.
- Tested on PHP 7.3 final !

### Improved

- Q&A: travis now tests with lowest supported deps !


## 0.8.3 (2018-12-03)

### Updated

- Support for `symfony/process 4.2`.

## 0.8.2 (2018-10-25)

### Added

- `VideoThumbGenerator::makeThumbnail()` now checks that output file exists and eventually throws `NoOutputGeneratedException`. [17](https://github.com/soluble-io/soluble-mediatools/issues/17)
- `VideoThumbParams::withFrame()` allows to make a thumbnail of a specific frame.
- `SelectFilter` added for frame selections.
- `VideoFilterChain::count()` method added, implements `Countable`

### Fixed

- `VideoThumbgenerator` did not honour default number of threads (minor)
- `VideoFilterChain` prevent recursive chaining (merge chains)

## 0.8.1 (2018-10-25)

### Fixed

- Added `ext-pcre` to required deps
- Minor, added `UnexpectedValueException` to handle case when ffmpeg cli command cannot be generated.

## 0.8.0 (2018-08-27)

### Added

- Added built-in video filter: `CropFilter`.
- Added more params to `ScaleFilter`.
- Improved error reporting with initial support for ffmpeg parameters validation `FFMpegParamValidator`.
  In order to fail earlier, a validation class now checks for some parameters values (currently CRF).
- `ActionParamInterface::getParam($name, $default=null)`, now allows to set a $default instead of always throwing exception.

### Fixed

- [BC-Break] Minor -> `InvalidReaderParamException` renamed into `InvalidParamException`.
- [BC-Break] Minor -> `UnsetParamReaderException` renamed into `UnsetParamException`.

### Documentation

- Added recipe for conversion with interlace detection
- Added chapter about compression (cbr, vbr)
- A lot of fixes and additions


## 0.7.7 (2018-08-19)

### Added

- Added `ScaleFilter` for scaling videos.

### Improved

- Thumbnail generation is now more performant *(seek time `-ss` is now before the `-i input file` option)*

### Changed

- Internal: `FFmpegAdapter` now uses `-filter:v` instead of `-vf` for clarity.

## 0.7.6 (2018-08-16)

### Added

- `VideoConvertParams::withAutoAltRef()` and `withLagInFrames()` to optimize vp9.

## 0.7.5 (2018-08-16)

### Added

- `UnescapedFileInterface` to allow setting outputfile without escaping.
- `PlatformNullFile()` can be set as `$outputFile` in `VideoConvert`.

## 0.7.4 (2018-08-16)

### Added

- `VideoConvertParams::withPass(int $passNumber)` to indicate ffmpeg pass number

### Documentation

- Added multipass params, example fixed using `VideoConvertParams::withPass()`.

## 0.7.3 (2018-08-15)

### Added

- `VideoConvertParams::withConvertParams(VideoConvertParamsInterface $extraParams)` convenience method to add/merge new params.

### Documentation

- Added doc for `VideoConvertParams::withConvertParams(VideoConvertParamsInterface $extraParams)`.

## 0.7.2 (2018-08-15)

### Added

- `VideoConvertParams::setPassLogFile(string $file)` to permit multipass encoding.
- `VideoInfo::getFileSize()` to get the filesize in bytes.
- `Common\Exception\IOException` and `IOExceptionInterface` for generic file/io exception

### Changed

- `FileNotFoundException`, `FileNotReadableException` now extends `IOException`

### Documentation

- Added example recipe for multipass transcoding

## 0.7.1 (2018-08-09)

### Added

- `VideoInfo::getFormatName()` to get the format name(s).
- `VideoInfo::countStreams()` to get the number of available streams.

### Improved

- Better error reporting when video file is not readable.

### Fixed

- `ext-json` added as requirement in composer.json

## 0.7.0 (2018-08-09)

**WARNING THIS IS BC-BREAK RELEASE**

### CHANGED

#### Conversion

> Search and replace `ConversionService` to `VideoConverter`.
> Search and replace `ConversionParams` to `VideoConvertParams`.

- [BC-BREAK] Renamed `ConversionService` to `VideoConverter`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)
- [BC-BREAK] Renamed `ConversionParams` to `VideoConvertParams`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)
- [BC-BREAK] Renamed `ConversionServiceInterface` to `VideoConverterInterface`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)
- [BC-BREAK] Renamed `ConversionServiceFactory` to `VideoConverterFactory`, [#1](https://github.com/soluble-io/soluble-mediatools/issues/1)

#### VideoQuery

> Search and replace `InfoService` to `VideoInfoReader`.
> Search and replace `Info` to `VideoInfo`.

- [BC-BREAK] Renamed `InfoService` to `VideoInfoReader`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)
- [BC-BREAK] Renamed `InfoServiceInterface` to `VideoInfoReaderInterface`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)
- [BC-BREAK] Renamed `InfoServiceFactory` to `VideoInfoReaderFactory`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)
- [BC-BREAK] Renamed `Info` to `VideoInfo`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)
- [BC-BREAK] Renamed `InfoInterface` to `VideoInfoInterface`, [#2](https://github.com/soluble-io/soluble-mediatools/issues/2)

#### Thumbnail

> Search and replace `ThumbService` to `VideoThumbGenerator`.

- [BC-BREAK] Renamed `ThumbService` to `VideoThumbGenerator`, [#3](https://github.com/soluble-io/soluble-mediatools/issues/3)
- [BC-BREAK] Renamed `ThumbParams` to `VideoThumbParams`, [#3](https://github.com/soluble-io/soluble-mediatools/issues/3)
- [BC-BREAK] Renamed `ThumbServiceInterface` to `VideoThumbGeneratorInterface`, [#3](https://github.com/soluble-io/soluble-mediatools/issues/3)
- [BC-BREAK] Renamed `ThumbServiceFactory` to `VideoThumbGeneratorFactory`, [#3](https://github.com/soluble-io/soluble-mediatools/issues/3)

#### Detection / Analyzer

> Search and replace `DetectionService` to `VideoAnalyzer`.

- [BC-BREAK] Renamed `DetectionService` to `VideoAnalyzer`, [#4](https://github.com/soluble-io/soluble-mediatools/issues/4)
- [BC-BREAK] Renamed `DetectionServiceInterface` to `VideoAnalyzerInterface`, [#4](https://github.com/soluble-io/soluble-mediatools/issues/4)
- [BC-BREAK] Renamed `DetectionServiceFactory` to `VideoAnalyzerFactory`, [#4](https://github.com/soluble-io/soluble-mediatools/issues/4)


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
