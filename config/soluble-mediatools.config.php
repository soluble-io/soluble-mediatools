<?php
/**
 * Default configuration file
 */

return [

    'soluble-mediatools' => [
        /*
        |--------------------------------------------------------------------------
        | FFMPEG section
        |--------------------------------------------------------------------------
        |
        | Options that will be used to create a FFMpegConfig object.
        |
        | @see \Soluble\MediaTools\Video\Config\FFMpegConfigFactory
        | @link https://github.com/soluble-io/soluble-mediatools/blob/master/src/Config/FFMpegConfigFactory.php
        */

        'ffmpeg.binary'         => 'ffmpeg',   // Or a complete path /opt/local/ffmpeg/bin/ffmpeg
        'ffmpeg.threads'        => null,       // <null>: single thread; <0>: number of cores, <1+>: number of threads
        'ffmpeg.timeout'        => null,       // <null>: no timeout, <number>: number of seconds before timing-out
        'ffmpeg.idle_timeout'   => null,       // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'ffmpeg.env'            => [],         // An array of additional env vars to set when running the ffmpeg conversion process


        /*
        |--------------------------------------------------------------------------
        | FFPROBE section
        |--------------------------------------------------------------------------
        |
        | Options that will be used to create a FFProbeConfig object.
        |
        | @see \Soluble\MediaTools\Video\Config\FFProbeConfigFactory
        | @link https://github.com/soluble-io/soluble-mediatools/blob/master/src/Config/FFProbeConfigFactory.php
        */

        'ffprobe.binary'        => 'ffprobe',  // Or a complete path /opt/local/ffmpeg/bin/ffprobe
        'ffprobe.timeout'       => null,       // <null>: no timeout, <number>: number of seconds before timing-out
        'ffprobe.idle_timeout'  => null,       // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'ffprobe.env'           => [],         // An array of additional env vars to set when running the ffprobe
    ],
];
