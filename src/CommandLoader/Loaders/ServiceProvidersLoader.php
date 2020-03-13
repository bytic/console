<?php

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Container\Container;
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
    protected $providers = null;

    /**
     * @return AbstractServiceProvider[]|ProviderRepository
     */
    public function getProviders(): array
    {
        if ($this->providers === null) {
            $this->setProvidersFromContainer($this->getContainer());
        }
        return $this->providers;
    }

    /**
     * @param Container $container
     */
    public function setProvidersFromContainer(Container $container): void
    {
        $providers = $container->getProviders();
        if ($providers instanceof ProviderRepository) {
            $this->setProviders($providers->getProviders());
            return;
        }
        $this->setProviders($providers);
        return;
    }

    /**
     * @param AbstractServiceProvider[]|ProviderRepository $providers
     */
    public function setProviders($providers): void
    {
        $this->providers = $providers;
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
