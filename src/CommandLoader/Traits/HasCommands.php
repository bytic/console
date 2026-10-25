<?php

declare(strict_types=1);

namespace ByTIC\Console\CommandLoader\Traits;

use ByTIC\Console\Command;
use Closure;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\LogicException;

trait HasCommands
{
    /** @var array<string, SymfonyCommand> */
    protected array $commands = [];

    public function addCommand(SymfonyCommand $command): void
    {
        $names = array_merge([$command->getName()], $command->getAliases());
        foreach ($names as $name) {
            if (!is_string($name) || $name === '') {
                throw new LogicException('Commands must have a non-empty name.');
            }
            if (isset($this->commands[$name]) && $this->commands[$name] !== $command) {
                throw new LogicException(sprintf('Duplicate command name or alias "%s".', $name));
            }
        }
        foreach ($names as $name) {
            $this->commands[$name] = $command;
        }
    }

    /** @return array<string, SymfonyCommand> */
    public function getCommands(): array
    {
        $this->checkLoaded();

        return $this->commands;
    }

    /** @param array<array-key, SymfonyCommand> $commands */
    public function setCommands(array $commands): void
    {
        $previous = $this->commands;
        $this->commands = [];
        try {
            foreach ($commands as $command) {
                $this->addCommand($command);
            }
        } catch (\Throwable $exception) {
            $this->commands = $previous;
            throw $exception;
        }
    }

    /** @param array<array-key, SymfonyCommand|class-string<SymfonyCommand>|Closure> $commands */
    public function resolveCommands(array $commands): static
    {
        foreach ($commands as $command) {
            $this->addCommand($this->resolve($command));
        }

        return $this;
    }

    public function resolve(mixed $command): SymfonyCommand
    {
        if ($command instanceof Closure) {
            $command = $command();
        } elseif (is_string($command)) {
            $command = $this->getContainer()->get($command);
        }

        if (!$command instanceof SymfonyCommand) {
            throw new LogicException('Command registrations must resolve to a Symfony command.');
        }
        if ($command instanceof Command) {
            $command->setContainer($this->getContainer());
        }

        return $command;
    }
}
