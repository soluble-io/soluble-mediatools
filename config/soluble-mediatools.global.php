<?php

return [
    'soluble-mediatools' => [

        /**
         * Binaries
         */
        'ffmpeg.binary'         => 'ffmpeg',   // or a complete path /opt/local/ffmpeg/bin/ffmpeg
        'ffprobe.binary'        => 'ffprobe',  // or a complete path /opt/local/ffmpeg/bin/ffprobe

        /**
         * Conversion service options
         */
        'conversion.threads'      => null,   // <null>: single thread; <0>: number of cores, <1+>: number of threads
        'conversion.timeout'      => null,   // <null>: no timeout, <number>: number of seconds before timing-out
        'conversion.idle_timeout' => 60,     // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'conversion.env'          => []      // An array of additional env vars to set when running the ffmpeg conversion process
    ],
];
