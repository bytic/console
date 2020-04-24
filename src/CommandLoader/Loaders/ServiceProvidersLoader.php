<?php

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Application\Application;
use Nip\Container\Container;
use Nip\Container\ServiceProviders\ProviderRepository;
use Nip\Container\ServiceProviders\Providers\AbstractServiceProvider;
use Psr\Container\ContainerInterface;

/**
 * Class ServiceProviders
 * @package ByTIC\Console\CommandLoader\Loaders
 */
class ServiceProvidersLoader extends AbstractLoader
{
    /**
     * @var AbstractServiceProvider[]|ProviderRepository
     */
    protected static $providers = null;

    /**
     * @return AbstractServiceProvider[]|ProviderRepository
     */
    public function getProviders(): array
    {
        if (self::$providers === null) {
            $this->setProvidersFromContainer($this->getContainer());
        }
        return self::$providers;
    }

    /**
     * @param ContainerInterface $container
     * @deprecated in 1.0 providers and in application
     */
    public function setProvidersFromContainer(ContainerInterface $container): void
    {
        $providers = [];
        if (method_exists($container, 'getProviderRepository') ) {
            $providers = $container->getProviderRepository();
            if ($providers instanceof ProviderRepository) {
                static::setProviders($providers->getProviders());
                return;
            } else {
                $providers = [];
            }
        }

        static::setProviders($providers);
        return;
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
        $providers = $this->getProviders();
        $commands = [];
        foreach ($providers as $provider) {
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
