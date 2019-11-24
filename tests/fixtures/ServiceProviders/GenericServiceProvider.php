<?php

namespace ByTIC\Console\Tests\Fixtures\ServiceProviders;

/**
 * Class GenericServiceProvider
 * @package ByTIC\Console\Tests\Fixtures\ServiceProviders
 */
class GenericServiceProvider extends \Nip\Container\ServiceProvider\AbstractServiceProvider
{

    public function registerCommands()
    {
        $this->commands('SomeCommand');
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        // TODO: Implement provides() method.
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}