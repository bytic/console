<?php

if (defined('BYTIC_CONSOLE_COMPOSER_INSTALL')) {
    return;
}

$deployFilePath = dirname(__DIR__);

$autoload = [
    $deployFilePath . '/vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
];

foreach ($autoload as $path) {
    if (file_exists($path)) {
        define('BYTIC_CONSOLE_COMPOSER_INSTALL', $path);
        define('BYTIC_CONSOLE_ROOT_DIR', realpath(dirname($path) . '/..'));
        break;
    }
}
unset($path);