<?php

use ByTIC\Console\Application;
use Nip\Container\Container;

if (defined('BYTIC_CONSOLE_ROOT_DIR')) {
    $appFile = BYTIC_CONSOLE_ROOT_DIR . '/bootstrap/app.php';
    if (file_exists($appFile)) {
        /** @noinspection PhpIncludeInspection */
        $app = require_once $appFile;
        $app->registerConfiguredProviders();
        /** @var Container $container */
        $container = $app->getContainer();
        \ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader::setProviders($container->getProviders());
    }
}
/** @var Container $container */
$container = $container ? $container : Container::getInstance();

/** @var ByTIC\Console\Application $app */
$app = $container->get(Application::class);
$app->setCommandLoader();