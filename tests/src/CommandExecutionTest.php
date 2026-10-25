<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests;

use ByTIC\Console\Application;
use ByTIC\Console\Command;
use ByTIC\Console\CommandLoader\CommandLoader;
use ByTIC\Console\Tests\Fixtures\Commands\Dependency;
use ByTIC\Console\Tests\Fixtures\Commands\DependencyCommand;
use Nip\Container\Container;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class CommandExecutionTest extends AbstractTest
{
    public function testHandleAndIoHelpers(): void
    {
        $command = new class ('test:io') extends Command {
            protected function configure(): void
            {
                $this->addArgument('value');
                $this->addOption('flag');
            }

            public function handle(): int
            {
                $this->info($this->argument('value'));
                $this->warn($this->option('flag') ? 'flagged' : 'unflagged');
                $this->line('debug only', verbosity: 'vvv');
                $this->table(['value'], (static function () {
                    yield ['row'];
                })());

                return self::SUCCESS;
            }
        };
        $tester = new CommandTester($command);
        self::assertSame(0, $tester->execute(['value' => 'hello', '--flag' => true]));
        self::assertStringContainsString('hello', $tester->getDisplay());
        self::assertStringContainsString('flagged', $tester->getDisplay());
        self::assertStringContainsString('row', $tester->getDisplay());
        self::assertStringNotContainsString('debug only', $tester->getDisplay());
    }

    public function testVoidHandlersRemainSuccessful(): void
    {
        $command = new class ('test:void') extends Command {
            public function handle(): void {}
        };
        self::assertSame(0, (new CommandTester($command))->execute([]));
    }

    public function testInvalidHandlerReturnIsRejected(): void
    {
        $command = new class ('test:invalid') extends Command {
            public function handle(): string
            {
                return 'invalid';
            }
        };
        $this->expectException(\Symfony\Component\Console\Exception\LogicException::class);
        (new CommandTester($command))->execute([]);
    }

    public function testExecuteOverrideIsSupported(): void
    {
        $command = new class ('test:execute') extends Command {
            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                $this->info('execute');

                return self::INVALID;
            }
        };
        $tester = new CommandTester($command);
        self::assertSame(2, $tester->execute([]));
        self::assertStringContainsString('execute', $tester->getDisplay());
    }

    public function testNestedCommandsResolveNamesClassesAndInstances(): void
    {
        $container = new Container();
        $container->set(Dependency::class, new Dependency('nested output'));
        $application = new Application();
        $application->setContainer($container);
        $loader = new CommandLoader();
        $loader->setContainer($container);
        $loader->setLoaders([]);
        $nested = new DependencyCommand(new Dependency('nested output'));
        $loader->addCommand($nested);
        $application->setCommandLoader($loader);

        $parent = new class ('test:parent') extends Command {
            public SymfonyCommand|string $child;
            public bool $silent = false;

            public function handle(): int
            {
                return $this->silent ? $this->callSilent($this->child) : $this->call($this->child);
            }
        };
        $parent->setApplication($application);
        foreach (['test:dependency', DependencyCommand::class, $nested] as $registration) {
            $parent->child = $registration;
            $tester = new CommandTester($parent);
            self::assertSame(1, $tester->execute([]));
            self::assertStringContainsString('nested output', $tester->getDisplay());
            $parent->silent = true;
            self::assertSame(1, $tester->execute([]));
            self::assertSame('', $tester->getDisplay());
            $parent->silent = false;
        }
    }

    public function testNestedInputInheritsNonInteractiveMode(): void
    {
        $child = new class ('test:child') extends Command {
            public function handle(): int
            {
                return $this->input->isInteractive() ? 1 : 0;
            }
        };
        $parent = new class ('test:parent') extends Command {
            public Command $child;

            public function handle(): int
            {
                return $this->call($this->child);
            }
        };
        $parent->setContainer(new Container());
        $parent->child = $child;
        self::assertSame(0, (new CommandTester($parent))->execute([], ['interactive' => false]));
    }

    public function testInteractiveHelpers(): void
    {
        $command = new class ('test:questions') extends Command {
            public array $answers;

            public function handle(): int
            {
                $this->answers = [
                    $this->ask('Name'),
                    $this->confirm('Continue?'),
                    $this->choice('Select', ['first', 'second'], multiple: true),
                    $this->secret('Password'),
                ];

                return self::SUCCESS;
            }
        };
        $tester = new CommandTester($command);
        $tester->setInputs(['Alice', 'yes', '0,1', 'secret']);
        self::assertSame(0, $tester->execute([], ['interactive' => true]));
        self::assertSame(['Alice', true, ['first', 'second'], 'secret'], $command->answers);
    }
}
