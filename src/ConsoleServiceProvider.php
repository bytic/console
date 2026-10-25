<?php

declare(strict_types=1);

namespace ByTIC\Console;

use ByTIC\Console\CommandLoader\CommandLoader;
use Nip\Container\Container;
use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

class ConsoleServiceProvider extends AbstractSignatureServiceProvider
{
    public function register(): void
    {
        $this->registerCommandLoader();
        $this->registerApplication();
    }

    public function registerApplication(): void
    {
        $container = $this->consoleContainer();
        if (!$container->initialized(Application::class) && !$container->hasDefinition(Application::class)) {
            $container->share(Application::class, static function () use ($container): Application {
                static $application;
                if ($application === null) {
                    $application = new Application();
                    $application->setContainer($container);
                    $application->setCommandLoader($container->get(CommandLoader::class));
                }

                return $application;
            });
        } else {
            $application = $container->get(Application::class);
            if (!$application instanceof Application) {
                throw new \LogicException('The console application service must be a ByTIC console application.');
            }
            $application->setContainer($container);
            $application->setCommandLoader($container->get(CommandLoader::class));
        }
        $container->alias(Application::class, 'console.application');
    }

    public function registerCommandLoader(): void
    {
        $container = $this->consoleContainer();
        if (!$container->initialized(CommandLoader::class) && !$container->hasDefinition(CommandLoader::class)) {
            $container->share(CommandLoader::class, static function () use ($container): CommandLoader {
                static $loader;
                if ($loader === null) {
                    $loader = new CommandLoader();
                    $loader->setContainer($container);
                }

                return $loader;
            });
        } else {
            $loader = $container->get(CommandLoader::class);
            if (!$loader instanceof CommandLoader) {
                throw new \LogicException('The console command loader service must be a ByTIC command loader.');
            }
            $loader->setContainer($container);
        }
        $container->alias(CommandLoader::class, 'console.command-loader');
    }

    public function provides(): array
    {
        return [Application::class, CommandLoader::class, 'console.application', 'console.command-loader'];
    }

    private function consoleContainer(): Container
    {
        $container = $this->getContainer();
        if (!$container instanceof Container) {
            throw new \LogicException('ConsoleServiceProvider requires a ByTIC container.');
        }

        return $container;
    }
}
