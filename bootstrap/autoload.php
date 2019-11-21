<?php

$deployFilePath = dirname(__DIR__);

$autoload = [
    $deployFilePath . '/vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
];

foreach ($autoload as $path) {
    if (file_exists($path)) {
        return require $path;
    }
}
return false;