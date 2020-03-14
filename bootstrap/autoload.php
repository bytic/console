<?php

require 'constants.php';

if (!defined('BYTIC_CONSOLE_COMPOSER_INSTALL')) {
    throw new \Exception('Could not resolve path to vendor/autoload.php');
}

require BYTIC_CONSOLE_COMPOSER_INSTALL;
