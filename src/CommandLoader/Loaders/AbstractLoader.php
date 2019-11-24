<?php

namespace ByTIC\Console\CommandLoader\Loaders;

/**
 * Class AbstractLoader
 * @package ByTIC\Console\CommandLoader\Loaders
 */
abstract class AbstractLoader
{
    protected $commands = null;

    /**
     * @return array
     */
    public function getCommands()
    {
        if ($this->commands === null) {
            $this->commands = $this->generateCommands();
        }
        return $this->commands;
    }

    /**
     * @return array
     */
    abstract protected function generateCommands() : array;
}
