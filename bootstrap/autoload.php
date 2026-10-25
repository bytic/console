<?php

declare(strict_types=1);

require __DIR__ . '/constants.php';

if (!defined('BYTIC_CONSOLE_COMPOSER_INSTALL')) {
    throw new RuntimeException('Could not resolve vendor/autoload.php. Run composer install first.');
}

require_once BYTIC_CONSOLE_COMPOSER_INSTALL;
