<?php

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Container\ServiceProvider\AbstractServiceProvider;
use Nip\Container\ServiceProvider\ProviderRepository;

/**
 * Class ServiceProviders
 * @package ByTIC\Console\CommandLoader\Loaders
 */
class ServiceProvidersLoader extends AbstractLoader
{
    /**
     * @var AbstractServiceProvider[]|ProviderRepository
     */
    protected static $providers;

    /**
     * @return AbstractServiceProvider[]|ProviderRepository
     */
    public static function getProviders(): array
    {
        return self::$providers;
    }

    /**
     * @param AbstractServiceProvider[]|ProviderRepository $providers
     */
    public static function setProviders($providers): void
    {
        self::$providers = $providers;
    }

    /**
     * @return array
     */
    protected function generateCommands(): array
    {
        $commands = [];
        foreach (static::$providers as $provider) {
            $commands = array_merge($commands, $this->loadCommandsFromProvider($provider));
        }
        return $commands;
    }

    /**
     * @param AbstractServiceProvider $provider
     * @return array
     */
    protected function loadCommandsFromProvider($provider)
    {
        return [];
    }
}
