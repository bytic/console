<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests;

use ByTIC\Console\Application;
use ByTIC\Console\CommandLoader\CommandLoader;
use ByTIC\Console\ConsoleServiceProvider;
use Nip\Container\Container;
use Symfony\Component\Console\Tester\ApplicationTester;

class ConsoleServiceProviderTest extends AbstractTest
{
    public function testServicesShareTheConfiguredContainer(): void
    {
        $container = new Container();
        $provider = new ConsoleServiceProvider();
        $provider->setContainer($container);
        $provider->register();

        $application = $container->get('console.application');
        self::assertInstanceOf(Application::class, $application);
        self::assertSame($application, $container->get(Application::class));
        self::assertSame($container, $application->getContainer());
        self::assertSame($container->get(CommandLoader::class), $container->get('console.command-loader'));
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        self::assertSame(0, $tester->run(['command' => 'list']));
    }

    public function testApplicationsDoNotShareContainers(): void
    {
        $applications = [];
        foreach ([new Container(), new Container()] as $container) {
            $provider = new ConsoleServiceProvider();
            $provider->setContainer($container);
            $provider->register();
            $applications[] = $container->get(Application::class);
        }
        self::assertNotSame($applications[0], $applications[1]);
        self::assertNotSame($applications[0]->getContainer(), $applications[1]->getContainer());
    }

    public function testExistingServicesArePreservedAndWired(): void
    {
        $container = new Container();
        $application = new Application('Custom');
        $loader = new CommandLoader();
        $container->set(Application::class, $application);
        $container->set(CommandLoader::class, $loader);
        $provider = new ConsoleServiceProvider();
        $provider->setContainer($container);
        $provider->register();
        self::assertSame($application, $container->get('console.application'));
        self::assertSame($loader, $container->get('console.command-loader'));
        self::assertSame($container, $application->getContainer());
        self::assertSame($container, $loader->getContainer());
    }
}
