<?php

namespace ByTIC\Console\Tests\CommandLoader\Traits;

use ByTIC\Console\CommandLoader\CommandLoader;
use ByTIC\Console\CommandLoader\Loaders\AbstractLoader;
use ByTIC\Console\Tests\AbstractTest;
use Nip\Container\Container;

/**
 * Class HasLoadersTest
 * @package ByTIC\Console\Tests\CommandLoader\Traits
 */
class HasLoadersTest extends AbstractTest
{
    public function test_getLoaders()
    {
        $container = new Container();
        $commandLoader = new CommandLoader();
        $commandLoader->setContainer($container);

        $loaders = $commandLoader->getLoaders();
        self::assertIsArray($loaders);
        self::assertCount(2, $loaders);
        foreach ($loaders as $loader) {
            self::assertInstanceOf(AbstractLoader::class, $loader);
            self::assertSame($container, $loader->getContainer());
        }
    }
}