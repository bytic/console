<?php

use ByTIC\Console\Application;
use ByTIC\Console\CommandLoader\CommandLoader;
use Nip\Container\Container;

if (defined('BYTIC_CONSOLE_ROOT_DIR')) {
    $appFile = realpath(BYTIC_CONSOLE_ROOT_DIR . '/bootstrap/app.php');
    if ($appFile != __FILE__ && file_exists($appFile)) {
        /** @noinspection PhpIncludeInspection */
        $app = require_once $appFile;
        $app->registerConfiguredProviders();
        /** @var Container $container */
        $container = $app->getContainer();
        \ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader::setProvidersFromContainer($container);
    }
}
/** @var Container $container */
if (!isset($container)) {
    $container = Container::getInstance();
}
if (!($container instanceof Container)) {
    $container = new Container();
    Container::setInstance($container);
}


/** @var ByTIC\Console\Application $app */
$app = $container->get(Application::class);
$commandLoader = $container->get(CommandLoader::class);

$app->setCommandLoader($commandLoader);

return $app;