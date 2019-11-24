<?php

namespace ByTIC\Console;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;

/**
 * Class ConsoleServiceProvider
 * @package Nip\Router
 */
class ConsoleServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerApplication();
        $this->registerCommandLoader();
    }

    public function registerApplication()
    {
    }

    public function registerCommandLoader()
    {
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['console.application','console.command-loader'];
    }
}
