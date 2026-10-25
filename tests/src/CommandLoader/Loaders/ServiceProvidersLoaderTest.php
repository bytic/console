<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\CommandLoader\Loaders;

use ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader;
use ByTIC\Console\Tests\AbstractTest;
use ByTIC\Console\Tests\Fixtures\ServiceProviders\GenericServiceProvider;
use Nip\Container\Container;

/**
 * Class ServiceProvidersLoaderTest
 */
class ServiceProvidersLoaderTest extends AbstractTest
{
    public function test_getProviders_empty_container()
    {
        $container = new Container();
        $loader = new ServiceProvidersLoader();
        $loader->setContainer($container);

        $providers = $loader->getProviders();
        self::assertIsArray($providers);
        self::assertCount(0, $providers);
    }

    public function test_generate_commands()
    {
        $loader = new ServiceProvidersLoader();
        $loader->setProviders([new GenericServiceProvider()]);

        $commands = $loader->getCommands();
        self::assertCount(1, $commands);
    }

    public function testProvidersAreInstanceScoped(): void
    {
        $first = new ServiceProvidersLoader();
        $first->setProviders([new GenericServiceProvider()]);
        $second = new ServiceProvidersLoader();
        $second->setContainer(new Container());
        self::assertCount(1, $first->getCommands());
        self::assertSame([], $second->getCommands());
        $first->setProviders([]);
        self::assertSame([], $first->getCommands());
    }
}
