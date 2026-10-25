<?php

declare(strict_types=1);

if (!defined('BYTIC_CONSOLE_COMPOSER_INSTALL')) {
    $autoloaders = [
        $GLOBALS['_composer_autoload_path'] ?? '',
        dirname(__DIR__) . '/vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
    ];

    foreach ($autoloaders as $autoload) {
        if ($autoload !== '' && is_file($autoload)) {
            define('BYTIC_CONSOLE_COMPOSER_INSTALL', realpath($autoload));
            break;
        }
    }
    unset($autoloaders, $autoload);
}

if (!defined('BYTIC_CONSOLE_ROOT_DIR') && defined('BYTIC_CONSOLE_COMPOSER_INSTALL')) {
    define('BYTIC_CONSOLE_ROOT_DIR', dirname(BYTIC_CONSOLE_COMPOSER_INSTALL, 2));
}
