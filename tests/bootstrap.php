<?php

declare(strict_types=1);

if (!defined('FFMPEG_BINARY_PATH')) {
    throw new \Exception('Missing phpunit constant FFMPEG_BINARY_PATH');
}
if (!defined('FFPROBE_BINARY_PATH')) {
    throw new \Exception('Missing phpunit constant FFPROBE_BINARY_PATH');
}
