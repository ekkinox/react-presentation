<?php

use React\EventLoop\Factory;
use React\EventLoop\Timer\Timer;
use React\Stream\Stream;

require __DIR__ . '/../vendor/autoload.php';

$start = microtime(true);

$files = [
    'node-v0.6.18.tar.gz'           => 'http://nodejs.org/dist/v0.6.18/node-v0.6.18.tar.gz',
    'php-5.5.15.tar.gz'             => 'http://it.php.net/get/php-5.5.15.tar.gz/from/this/mirror',
    'node-v0.11.9-sunos-x64.tar.gz' => 'http://nodejs.org/dist/v0.11.9/node-v0.11.9-sunos-x64.tar.gz',
    'node-v0.11.9-sunos-x86.tar.gz' => 'http://nodejs.org/dist/v0.11.9/node-v0.11.9-sunos-x86.tar.gz',
];

$loop = Factory::create();

foreach ($files as $file => $url) {
    $readStream  = fopen($url, 'r');
    $writeStream = fopen($file, 'w');

    stream_set_blocking($readStream, 0);
    stream_set_blocking($writeStream, 0);

    $read  = new Stream($readStream, $loop);
    $write = new Stream($writeStream, $loop);

    $read->on('end', function () use ($file, &$files) {
        unset($files[$file]);

        echo "Finished downloading $file" . PHP_EOL;
    });

    $read->pipe($write);
}

$loop->addPeriodicTimer(1, function (Timer $timer) use (&$files) {
    if (0 === count($files)) {
        $timer->cancel();
    }

    echo "##### Loop tick #####" . PHP_EOL;

    foreach ($files as $file => $url) {
        $mbytes    = filesize($file) / (1024 * 1024);
        $formatted = number_format($mbytes, 3);

        echo "$file: $formatted MB\n";
    }
});

echo "This script will show the download status every 1 seconds." . PHP_EOL;

$loop->run();

$end = microtime(true);

echo "Execution time: " . ($end - $start);
