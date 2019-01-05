## From 0.8 to 0.9

- `VideoInfoInterface::getBitrate()` renamed to `VideoInfoInterface::getVideoBitrate()`

## From 0.7 to 0.8

- `Soluble\MediaTools\Video\Exception\UnsetParamReaderException` renamed into `UnsetParamException`.
- `Soluble\MediaTools\Video\Exception\InvalidReaderParamException` renamed into `InvalidParamException`.

## From <= 0.6 to 0.7

A lot of renaming after code review.

Search and replace
 
- `ConversionService` to `VideoConverter`.
- `ConversionParams` to `VideoConvertParams`.
- `InfoService` to `VideoInfoReader`.
- `Info` to `VideoInfo`.
- `ThumbService` to `VideoThumbGenerator`.
- `DetectionService` to `VideoAnalyzer`.
