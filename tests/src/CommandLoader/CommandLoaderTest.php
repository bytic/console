<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\CommandLoader;

use ByTIC\Console\CommandLoader\CommandLoader;
use ByTIC\Console\CommandLoader\Loaders\AbstractLoader;
use ByTIC\Console\Tests\AbstractTest;
use ByTIC\Console\Tests\Fixtures\Commands\Dependency;
use ByTIC\Console\Tests\Fixtures\Commands\DependencyCommand;
use ByTIC\Console\Tests\Fixtures\Commands\DisabledCommand;
use Nip\Container\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use ByTIC\Console\Application;

class CommandLoaderTest extends AbstractTest
{
    public function testMetadataDiscoveryIsLazyAndInstancesAreReused(): void
    {
        DependencyCommand::$constructions = 0;
        $container = new Container();
        $container->set(Dependency::class, new Dependency('from container'));
        $loader = new CommandLoader();
        $loader->setContainer($container);
        $discovery = $this->discovery([DependencyCommand::class]);
        $loader->setLoaders([$discovery]);

        self::assertSame(['test:dependency', 'test:alias'], $loader->getNames());
        self::assertTrue($loader->has('test:dependency'));
        self::assertSame(0, DependencyCommand::$constructions);
        self::assertSame(1, $discovery->calls);
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCommandLoader($loader);
        self::assertSame(0, (new ApplicationTester($application))->run(['command' => 'list']));
        self::assertSame(0, DependencyCommand::$constructions);
        self::assertSame($loader->get('test:dependency'), $loader->get('test:alias'));
        $tester = new CommandTester($loader->get('test:alias'));
        self::assertSame(Command::FAILURE, $tester->execute([]));
        self::assertStringContainsString('from container', $tester->getDisplay());
        $tester->execute([]);
        self::assertSame(1, DependencyCommand::$constructions);
    }

    public function testDuplicateNamesFailWithoutPartiallyLoading(): void
    {
        $loader = new CommandLoader();
        $loader->setContainer(new Container());
        $loader->setLoaders([$this->discovery([new Command('same'), new Command('same')])]);
        try {
            $loader->getNames();
            self::fail('Duplicate command names must fail.');
        } catch (LogicException $exception) {
            self::assertStringContainsString('Duplicate', $exception->getMessage());
        }
        $loader->setLoaders([$this->discovery([new Command('fixed')])]);
        self::assertSame(['fixed'], $loader->getNames());
    }

    public function testMissingCommandsHaveSymfonyException(): void
    {
        $loader = new CommandLoader();
        $loader->setContainer(new Container());
        $loader->setLoaders([]);
        $this->expectException(CommandNotFoundException::class);
        $loader->get('missing');
    }

    public function testInvalidFactoryResultsAreRejected(): void
    {
        $loader = new CommandLoader();
        $loader->setContainer(new Container());
        $this->expectException(LogicException::class);
        $loader->resolve(static fn() => new \stdClass());
    }

    public function testResetRediscoversCommands(): void
    {
        $loader = new CommandLoader();
        $loader->setContainer(new Container());
        $discovery = $this->discovery([new Command('before')]);
        $loader->setLoaders([$discovery]);
        self::assertSame(['before'], $loader->getNames());
        $discovery->registrations = [new Command('after')];
        $loader->reset();
        self::assertSame(['after'], $loader->getNames());
        self::assertSame(2, $discovery->calls);
    }

    public function testChangingContainerInvalidatesResolvedCommands(): void
    {
        $first = new Container();
        $first->set(Dependency::class, new Dependency('first'));
        $second = new Container();
        $second->set(Dependency::class, new Dependency('second'));
        $loader = new CommandLoader();
        $loader->setContainer($first);
        $loader->setLoaders([$this->discovery([DependencyCommand::class])]);
        $before = $loader->get('test:dependency');
        $loader->setContainer($second);
        $after = $loader->get('test:dependency');
        self::assertNotSame($before, $after);
        $tester = new CommandTester($after);
        $tester->execute([]);
        self::assertStringContainsString('second', $tester->getDisplay());
    }

    public function testDynamicAvailabilityIsPreservedForLazyCommands(): void
    {
        $loader = new CommandLoader();
        $loader->setContainer(new Container());
        $loader->setLoaders([$this->discovery([DisabledCommand::class])]);
        self::assertFalse($loader->get('test:disabled')->isEnabled());
    }

    public function testRecursiveFactoriesFailExplicitly(): void
    {
        $loader = new CommandLoader();
        $loader->setContainer(new Container());
        $loader->setLoaders([$this->discovery([static function () use ($loader): Command {
            $loader->getNames();

            return new Command('recursive');
        }])]);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Recursive command discovery');
        $loader->getNames();
    }

    private function discovery(array $registrations): AbstractLoader
    {
        return new class ($registrations) extends AbstractLoader {
            public int $calls = 0;

            public function __construct(public array $registrations) {}

            protected function generateCommands(): array
            {
                ++$this->calls;

                return $this->registrations;
            }
        };
    }
}
