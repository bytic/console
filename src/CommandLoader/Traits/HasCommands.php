<?php

namespace ByTIC\Console\CommandLoader\Traits;

use ByTIC\Console\Command;
use \Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Trait HasCommands
 * @package ByTIC\Console\CommandLoader\Traits
 */
trait HasCommands
{
    /**
     * @var SymfonyCommand[]
     */
    protected $commands = [];

    /**
     * @param SymfonyCommand $command
     */
    public function addCommand(SymfonyCommand $command)
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * @return SymfonyCommand[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param SymfonyCommand[] $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param array|mixed $commands
     * @return $this
     */
    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();
        foreach ($commands as $command) {
            $this->resolve($command);
        }
        return $this;
    }

    /**
     * Add a command, resolving through the application.
     *
     * @param string $command
     * @return SymfonyCommand
     */
    public function resolve($command)
    {
        if (is_string($command)) {
            /** @var SymfonyCommand $command */
            $command = new $command();
        }
        $this->addCommand($command);
        return $command;
    }
}