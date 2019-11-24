<?php

namespace ByTIC\Console\CommandLoader;

use ByTIC\Console\CommandLoader\Traits\HasCommands;
use ByTIC\Console\CommandLoader\Traits\HasLoaders;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Class CommandLoader
 * @package ByTIC\Console\CommandLoader
 */
class CommandLoader implements CommandLoaderInterface
{
    use HasLoaders;
    use HasCommands;

    /**
     * CommandLoader constructor.
     */
    public function __construct()
    {
        $this->initLoaders();
    }

    /**
     * @inheritDoc
     */
    public function get($name)
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
    public function has($name)
    {
        $this->checkLoaded();
        return isset($this->commands[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getNames()
    {
        $this->checkLoaded();
        return array_keys($this->commands);
    }
}