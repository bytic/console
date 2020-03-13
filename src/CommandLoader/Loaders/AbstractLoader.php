<?php

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Container\ContainerAwareTrait;

/**
 * Class AbstractLoader
 * @package ByTIC\Console\CommandLoader\Loaders
 */
abstract class AbstractLoader
{
    use ContainerAwareTrait;

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
