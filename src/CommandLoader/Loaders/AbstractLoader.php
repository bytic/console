<?php

declare(strict_types=1);

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Container\ContainerAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Class AbstractLoader
 */
abstract class AbstractLoader
{
    use ContainerAwareTrait {
        setContainer as private assignContainer;
    }

    protected ?array $commands = null;

    public function setContainer(ContainerInterface $container): static
    {
        if ($this->container !== $container) {
            $this->assignContainer($container);
            $this->reset();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        if ($this->commands === null) {
            $this->commands = $this->generateCommands();
        }
        return $this->commands;
    }

    /**
     * @return array
     */
    abstract protected function generateCommands(): array;

    public function reset(): void
    {
        $this->commands = null;
    }
}
