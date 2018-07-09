<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Detection;

use Soluble\MediaTools\Config\FFMpegConfigInterface;
use Soluble\MediaTools\Exception\FileNotFoundException;
use Soluble\MediaTools\Exception\UnsupportedParamException;
use Soluble\MediaTools\Exception\UnsupportedParamValueException;
use Soluble\MediaTools\Util\Assert\PathAssertionsTrait;
use Soluble\MediaTools\Util\PlatformNullFile;
use Soluble\MediaTools\Video\Converter\FFMpegAdapter;
use Soluble\MediaTools\Video\Exception\DetectionExceptionInterface;
use Soluble\MediaTools\Video\Exception\DetectionProcessExceptionInterface;
use Soluble\MediaTools\Video\Exception\InvalidParamException;
use Soluble\MediaTools\Video\Exception\MissingInputFileException;
use Soluble\MediaTools\Video\Exception\ProcessFailedException;
use Soluble\MediaTools\Video\Exception\RuntimeException;
use Soluble\MediaTools\Video\Filter\IdetVideoFilter;
use Soluble\MediaTools\VideoConversionParams;
use Symfony\Component\Process\Exception as SPException;
use Symfony\Component\Process\Process;

class InterlaceDetect
{
    use PathAssertionsTrait;

    public const DEFAULT_INTERLACE_MAX_FRAMES = 1000;

    /** @var FFMpegConfigInterface */
    protected $ffmpegConfig;

    /** @var FFMpegAdapter */
    protected $adapter;

    public function __construct(FFMpegConfigInterface $ffmpegConfig)
    {
        $this->ffmpegConfig = $ffmpegConfig;
        $this->adapter      = new FFMpegAdapter($ffmpegConfig);
    }

    /**
     * @throws DetectionExceptionInterface
     * @throws DetectionProcessExceptionInterface
     * @throws ProcessFailedException
     * @throws MissingInputFileException
     * @throws RuntimeException
     */
    public function guessInterlacing(string $file, int $maxFramesToAnalyze = self::DEFAULT_INTERLACE_MAX_FRAMES): InterlaceDetectGuess
    {
        $params = (new VideoConversionParams())
            ->withVideoFilter(new IdetVideoFilter()) // detect interlaced frames :)
            ->withVideoFrames($maxFramesToAnalyze)
            ->withNoAudio() // speed up the thing
            ->withOutputFormat('rawvideo')
            ->withOverwrite();

        try {
            $this->ensureFileExists($file);

            $arguments = $this->adapter->getMappedConversionParams($params);
            $ffmpegCmd = $this->adapter->getCliCommand($arguments, $file, new PlatformNullFile());

            $process = new Process($ffmpegCmd);
            $process->mustRun();
        } catch (FileNotFoundException $e) {
            throw new MissingInputFileException($e->getMessage());
        } catch (UnsupportedParamValueException | UnsupportedParamException $e) {
            throw new InvalidParamException($e->getMessage());
        } catch (SPException\ProcessFailedException | SPException\ProcessTimedOutException | SPException\ProcessSignaledException $e) {
            throw new ProcessFailedException($e->getProcess(), $e);
        } catch (SPException\RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        $stdErr = preg_split("/(\r\n|\n|\r)/", $process->getErrorOutput());

        // Counted frames
        $interlaced_tff = 0;
        $interlaced_bff = 0;
        $progressive    = 0;
        $undetermined   = 0;
        $total_frames   = 0;

        if ($stdErr !== false) {
            foreach ($stdErr as $line) {
                if (mb_substr($line, 0, 12) !== '[Parsed_idet') {
                    continue;
                }

                $unspaced = preg_replace('/( )+/', '', $line);
                $matches  = [];
                if (preg_match_all('/TFF:(\d+)BFF:(\d+)Progressive:(\d+)Undetermined:(\d+)/i', $unspaced, $matches) < 1) {
                    continue;
                }

                //$type = strpos(strtolower($unspaced), 'single') ? 'single' : 'multi';
                $interlaced_tff += (int) $matches[1][0];
                $interlaced_bff += (int) $matches[2][0];
                $progressive += (int) $matches[3][0];
                $undetermined += (int) $matches[4][0];
                $total_frames += ((int) $matches[1][0] + (int) $matches[2][0] + (int) $matches[3][0] + (int) $matches[4][0]);
            }
        }

        return new InterlaceDetectGuess($interlaced_tff, $interlaced_bff, $progressive, $undetermined);
    }
}
