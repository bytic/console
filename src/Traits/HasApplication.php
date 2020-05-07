<?php

namespace ByTIC\Console\Traits;

use Nip\Container\Container;

/**
 * Trait HasApplication
 * @package ByTIC\Console\Traits
 */
trait HasApplication
{

    /**
     * @return Container
     */
    public function getContainer()
    {
        return Container::getInstance();
    }

    /**
     * @param null|string $name
     * @return mixed|Container|object
     */
    protected function getFromContainer($name = null)
    {
        if (!$name) {
            return $this->getContainer();
        }

        return $this->getContainer()->get($name);
    }

    /**
     * @return mixed|Container|object
     */
    protected function getByticApp()
    {
        return $this->getFromContainer('app');
    }
}