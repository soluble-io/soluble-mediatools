<?php

return [
    'soluble-mediatools' => [

        // FFMPEG
        'ffmpeg.binary'         => 'ffmpeg',   // or a complete path /opt/local/ffmpeg/bin/ffmpeg
        'ffmpeg.threads'        => null,       // <null>: single thread; <0>: number of cores, <1+>: number of threads
        'ffmpeg.timeout'        => null,       // <null>: no timeout, <number>: number of seconds before timing-out
        'ffmpeg.idle_timeout'   => 60,         // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'ffmpeg.env'            => [],         // An array of additional env vars to set when running the ffmpeg conversion process

        // FFPROBE
        'ffprobe.binary'        => 'ffprobe',  // or a complete path /opt/local/ffmpeg/bin/ffprobe
        'ffprobe.timeout'       => null,       // <null>: no timeout, <number>: number of seconds before timing-out
        'ffprobe.idle_timeout'  => 60,         // <null>: no idle timeout, <number>: number of seconds of inactivity before timing-out
        'ffprobe.env'           => [],         // An array of additional env vars to set when running the ffprobe
    ],
];
