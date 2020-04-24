<?php

namespace ByTIC\Console\Tests\CommandLoader\Loaders;

use ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader;
use ByTIC\Console\Tests\AbstractTest;
use ByTIC\Console\Tests\Fixtures\ServiceProviders\GenericServiceProvider;
use Nip\Container\Container;

/**
 * Class ServiceProvidersLoaderTest
 * @package ByTIC\Console\Tests\CommandLoader\Loaders
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
        self::assertCount(0,$providers);
    }

    public function test_generate_commands()
    {
        $loader = new ServiceProvidersLoader();
        $loader->setProviders([new GenericServiceProvider()]);

        $commands = $loader->getCommands();
        self::assertCount(1, $commands);
    }
}
