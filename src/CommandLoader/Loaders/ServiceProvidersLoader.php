<?php

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Application\Application;
use Nip\Container\Container;
use Nip\Container\ServiceProviders\ProviderRepository;
use Nip\Container\ServiceProviders\Providers\AbstractServiceProvider;

/**
 * Class ServiceProviders
 * @package ByTIC\Console\CommandLoader\Loaders
 */
class ServiceProvidersLoader extends AbstractLoader
{
    /**
     * @var AbstractServiceProvider[]|ProviderRepository
     */
    protected static $providers = [];

    /**
     * @return AbstractServiceProvider[]|ProviderRepository
     */
    public static function getProviders(): array
    {
        return self::$providers;
    }

    /**
     * @param Application $application
     */
    public static function setProvidersFromApplication(Application $application): void
    {
        $providers = $application->getProviderRepository();
        if ($providers instanceof ProviderRepository) {
            static::setProviders($providers->getProviders());
            return;
        }
        static::setProviders($providers);
        return;
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
        if (method_exists($provider, 'getCommands')) {
            return $provider->getCommands();
        }
        return [];
    }
}
