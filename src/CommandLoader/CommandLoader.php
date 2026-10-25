<?php

declare(strict_types=1);

namespace ByTIC\Console\CommandLoader;

use ByTIC\Console\CommandLoader\Traits\HasCommands;
use ByTIC\Console\CommandLoader\Traits\HasLoaders;
use Nip\Container\ContainerAwareTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Class CommandLoader
 */
class CommandLoader implements CommandLoaderInterface
{
    use HasLoaders;
    use HasCommands;
    use ContainerAwareTrait {
        setContainer as private assignContainer;
    }

    public function setContainer(ContainerInterface $container): static
    {
        if ($this->container !== $container) {
            $this->assignContainer($container);
            foreach ($this->loaders ?? [] as $loader) {
                $loader->setContainer($container);
            }
            $this->reset();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): Command
    {
        $this->checkLoaded();
        if (!$this->has($name)) {
            throw new CommandNotFoundException("Command: {$name}");
        }
        return $this->commands[$name];
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        $this->checkLoaded();
        return isset($this->commands[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getNames(): array
    {
        $this->checkLoaded();
        return array_keys($this->commands);
    }
}
