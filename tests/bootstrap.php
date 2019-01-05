<?php

declare(strict_types=1);

/*
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

if (!defined('FFMPEG_BINARY_PATH')) {
    throw new \Exception('Missing phpunit constant FFMPEG_BINARY_PATH');
}
if (!defined('FFPROBE_BINARY_PATH')) {
    throw new \Exception('Missing phpunit constant FFPROBE_BINARY_PATH');
}
