<?php

namespace ByTIC\Console\CommandLoader\Traits;

use ByTIC\Console\CommandLoader\Loaders\AbstractLoader;
use ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader;

/**
 * Trait HasLoaders
 * @package ByTIC\Console\CommandLoader\Traits
 */
trait HasLoaders
{
    /**
     * @var null|AbstractLoader[]
     */
    protected $loaders = null;
    protected $isLoaded = false;

    /**
     * @return bool
     */
    protected function isLoaded(): bool
    {
        return $this->isLoaded;
    }

    protected function checkLoaded()
    {
        if ($this->isLoaded()) {
            return;
        }
        $loaders = $this->getLoaders();
        $commands = [];
        foreach ($loaders as $loader) {
            $commands = array_merge($commands, $loader->getCommands());
        }
        $this->resolveCommands($commands);
    }

    /**
     * @return AbstractLoader[]
     */
    public function getLoaders()
    {
        if ($this->loaders === null) {
            $this->initLoaders();
        }
        return $this->loaders;
    }

    protected function initLoaders()
    {
        $loaders = $this->getEnabledLoaders();
        foreach ($loaders as $loader) {
            $this->loaders[] = new $loader();
        }
    }

    /**
     * @return array
     */
    protected function getEnabledLoaders()
    {
        return [
            ServiceProvidersLoader::class,
        ];
    }
}