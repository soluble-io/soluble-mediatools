<?php

declare(strict_types=1);

$content = file_get_contents('./codecs.txt');
if ($content === false) {
    die('cannot locate codecs.txt file');
}
$lines  = explode("\n", $content);
$output = [];
$types  = [
    'A' => 'AUDIO',
    'D' => 'DATA',
    'S' => 'SUBTITLE',
    'V' => 'VIDEO',
];
foreach ($lines as $l) {
    $test = preg_match('/^(?P<type>([A|S|V|D]))\:(?P<codec>([a-z_\-0-9])+)(?P<rest>(.*))/', $l, $matches);
    if ($test !== false) {
        $type     = $matches['type'];
        $codec    = $matches['codec'];
        $comment  = trim($matches['rest']);
        $output[] = sprintf(
            "%s\npublic const %s_%s = '%s';",
            "/**\n " . $comment . "\n*/",
            $types[$type],
            mb_strtoupper($codec),
            $codec
        );
    }
}

echo implode("\n", $output);
