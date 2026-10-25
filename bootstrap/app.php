<?php

declare(strict_types=1);

use ByTIC\Console\Application;
use ByTIC\Console\ConsoleServiceProvider;
use ByTIC\Console\Support\ApplicationDetector;
use Nip\Container\Container;

$container = (new ApplicationDetector())->getContainer() ?? new Container();
Container::setInstance($container);

$provider = new ConsoleServiceProvider();
$provider->setContainer($container);
$provider->register();

return $container->get(Application::class);
