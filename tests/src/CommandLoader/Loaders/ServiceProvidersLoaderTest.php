<?php

namespace ByTIC\Console\Tests\CommandLoader\Loaders;

use ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader;
use ByTIC\Console\Tests\AbstractTest;
use ByTIC\Console\Tests\Fixtures\ServiceProviders\GenericServiceProvider;

/**
 * Class ServiceProvidersLoaderTest
 * @package ByTIC\Console\Tests\CommandLoader\Loaders
 */
class ServiceProvidersLoaderTest extends AbstractTest
{

    public function test_generate_commands()
    {
        $loader = new ServiceProvidersLoader();
        $loader::setProviders([new GenericServiceProvider()]);

        $commands = $loader->getCommands();
        self::assertCount(1, $commands);
    }
}
