<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\CommandLoader\Loaders;

use App\Console\Model\TestCommand;
use ByTIC\Console\CommandLoader\Loaders\FolderLoader;
use ByTIC\Console\Tests\AbstractTest;
use Nip\Application\Application;
use Nip\Container\Container;

/**
 * Class FolderLoaderTest
 */
class FolderLoaderTest extends AbstractTest
{
    public function test_generate_commands()
    {
        $container = new Container();

        $application = new Application();
        $container->set('app', $application);
        $container->set(
            'path',
            TEST_FIXTURE_PATH . DIRECTORY_SEPARATOR . 'demoapp' . DIRECTORY_SEPARATOR . 'application',
        );

        $loader = new FolderLoader();
        $loader->setContainer($container);

        $commands = $loader->getCommands();
        self::assertCount(1, $commands);
        self::assertSame(TestCommand::class, $commands[0]);
    }

    public function testExplicitNamespaceMapping(): void
    {
        $loader = new FolderLoader();
        $loader->setContainer(new Container());
        $loader->setNamespaces([
            TEST_FIXTURE_PATH . '/demoapp/application/Console' => 'App\\Console',
        ]);
        self::assertSame([TestCommand::class], $loader->getCommands());
    }

    public function testInvalidExplicitFoldersFail(): void
    {
        $loader = new FolderLoader();
        $this->expectException(\InvalidArgumentException::class);
        $loader->setFolders([TEST_FIXTURE_PATH . '/missing']);
    }

    public function testMissingConventionalDirectoryIsEmpty(): void
    {
        $loader = new FolderLoader();
        $loader->setContainer(new Container());
        self::assertSame([], $loader->getCommands());
    }
}
