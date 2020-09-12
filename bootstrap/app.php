<?php

use ByTIC\Console\Application;
use ByTIC\Console\CommandLoader\CommandLoader;
use ByTIC\Console\Support\ApplicationDetector;
use Nip\Container\Container;

$container = (new ApplicationDetector())->getContainer();

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
$commandLoader->setContainer($container);

Container::setInstance($container);

$app->setCommandLoader($commandLoader);

return $app;
