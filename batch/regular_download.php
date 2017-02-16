<?php

$start = microtime(true);

$files = [
    'node-v0.6.18.tar.gz'           => 'http://nodejs.org/dist/v0.6.18/node-v0.6.18.tar.gz',
    'php-5.5.15.tar.gz'             => 'http://it.php.net/get/php-5.5.15.tar.gz/from/this/mirror',
    'node-v0.11.9-sunos-x64.tar.gz' => 'http://nodejs.org/dist/v0.11.9/node-v0.11.9-sunos-x64.tar.gz',
    'node-v0.11.9-sunos-x86.tar.gz' => 'http://nodejs.org/dist/v0.11.9/node-v0.11.9-sunos-x86.tar.gz',
];

foreach ($files as $file => $url) {
    echo "Downloading file $file ... ";

    $readStream  = fopen($url, 'r');
    $writeStream = fopen($file, 'w');

    $streamSize = stream_copy_to_stream($readStream, $writeStream);

    $mbytes    = $streamSize / (1024 * 1024);
    $formatted = number_format($mbytes, 3);

    echo "download OK, size : $formatted MB" . PHP_EOL;
}

$end = microtime(true);

echo "Execution time: " . ($end - $start);
