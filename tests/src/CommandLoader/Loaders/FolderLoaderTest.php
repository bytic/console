<?php

namespace ByTIC\Console\Tests\CommandLoader\Loaders;

use App\Console\Model\TestCommand;
use ByTIC\Console\CommandLoader\Loaders\FolderLoader;
use ByTIC\Console\Tests\AbstractTest;
use Nip\Application\Application;
use Nip\Container\Container;

/**
 * Class FolderLoaderTest
 * @package ByTIC\Console\Tests\CommandLoader\Loaders
 */
class FolderLoaderTest extends AbstractTest
{

    public function test_generate_commands()
    {
        $container = new Container();

        $application = new Application();
        $container->set('app', $application);
        $container->set('path',
            TEST_FIXTURE_PATH . DIRECTORY_SEPARATOR . 'demoapp' . DIRECTORY_SEPARATOR . 'application');

        $loader = new FolderLoader();
        $loader->setContainer($container);

        $commands = $loader->getCommands();
        self::assertCount(1, $commands);
        self::assertSame(TestCommand::class, $commands[0]);
    }
}
