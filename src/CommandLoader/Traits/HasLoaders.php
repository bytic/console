<?php

declare(strict_types=1);

namespace ByTIC\Console\CommandLoader\Traits;

use ByTIC\Console\CommandLoader\Loaders\AbstractLoader;
use ByTIC\Console\CommandLoader\Loaders\FolderLoader;
use ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LazyCommand;

trait HasLoaders
{
    /** @var list<AbstractLoader>|null */
    protected ?array $loaders = null;
    protected bool $isLoaded = false;
    private bool $isLoading = false;

    protected function isLoaded(): bool
    {
        return $this->isLoaded;
    }

    protected function checkLoaded(): void
    {
        if ($this->isLoaded()) {
            return;
        }
        if ($this->isLoading) {
            throw new \LogicException('Recursive command discovery: a command factory requested the loader while it was loading.');
        }

        $previous = $this->commands;
        $this->isLoading = true;
        try {
            foreach ($this->getLoaders() as $loader) {
                foreach ($loader->getCommands() as $registration) {
                    $this->addCommand($this->createDiscoveredCommand($registration));
                }
            }
            $this->isLoaded = true;
        } catch (\Throwable $exception) {
            $this->commands = $previous;
            throw $exception;
        } finally {
            $this->isLoading = false;
        }
    }

    protected function createDiscoveredCommand(mixed $registration): Command
    {
        if (is_string($registration) && is_a($registration, Command::class, true)) {
            $attributes = (new \ReflectionClass($registration))->getAttributes(AsCommand::class);
            if ($attributes !== []) {
                $metadata = $attributes[0]->newInstance();
                $names = explode('|', $metadata->name);
                $hidden = $names[0] === '';
                if ($names[0] === '') {
                    array_shift($names);
                }
                $name = array_shift($names);
                if (!is_string($name) || $name === '') {
                    throw new \LogicException('Command attributes must declare a non-empty name.');
                }
                $hasDefaultEnabledCheck = (new \ReflectionMethod($registration, 'isEnabled'))
                    ->getDeclaringClass()->getName() === Command::class;

                return new LazyCommand(
                    $name,
                    $names,
                    $metadata->description ?? '',
                    $hidden,
                    fn(): Command => $this->resolve($registration),
                    $hasDefaultEnabledCheck ? true : null,
                );
            }
        }

        return $this->resolve($registration);
    }

    /** @return list<AbstractLoader> */
    public function getLoaders(): array
    {
        if ($this->loaders === null) {
            $this->initLoaders();
        }

        return $this->loaders;
    }

    /** @param list<AbstractLoader> $loaders */
    public function setLoaders(array $loaders): void
    {
        foreach ($loaders as $loader) {
            $loader->setContainer($this->getContainer());
        }
        $this->loaders = $loaders;
        $this->reset();
    }

    public function reset(): void
    {
        $this->commands = [];
        $this->isLoaded = false;
        foreach ($this->loaders ?? [] as $loader) {
            $loader->reset();
        }
    }

    protected function initLoaders(): void
    {
        $this->loaders = [];
        foreach ($this->getEnabledLoaders() as $class) {
            $loader = new $class();
            $loader->setContainer($this->getContainer());
            $this->loaders[] = $loader;
        }
    }

    /** @return list<class-string<AbstractLoader>> */
    protected function getEnabledLoaders(): array
    {
        return [ServiceProvidersLoader::class, FolderLoader::class];
    }
}
