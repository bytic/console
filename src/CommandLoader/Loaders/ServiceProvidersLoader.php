<?php

declare(strict_types=1);

namespace ByTIC\Console\CommandLoader\Loaders;

use Nip\Container\ServiceProviders\ProviderRepository;
use Psr\Container\ContainerInterface;

class ServiceProvidersLoader extends AbstractLoader
{
    /** @var list<object>|null */
    protected ?array $providers = null;

    /** @return list<object> */
    public function getProviders(): array
    {
        if ($this->providers !== null) {
            return $this->providers;
        }
        $container = $this->getContainer();
        $repository = method_exists($container, 'getProviderRepository')
            ? $container->getProviderRepository()
            : null;

        return $repository instanceof ProviderRepository ? array_values($repository->getProviders()) : [];
    }

    public function setProvidersFromContainer(ContainerInterface $container): void
    {
        $providers = method_exists($container, 'getProviderRepository')
            ? $container->getProviderRepository()
            : [];
        $this->setProviders($providers);
    }

    /** @param iterable<object> $providers */
    public function setProviders(iterable|ProviderRepository $providers): void
    {
        if ($providers instanceof ProviderRepository) {
            $providers = $providers->getProviders();
        }
        $this->providers = is_array($providers) ? array_values($providers) : iterator_to_array($providers, false);
        $this->reset();
    }

    protected function generateCommands(): array
    {
        $commands = [];
        foreach ($this->getProviders() as $provider) {
            if (method_exists($provider, 'getCommands')) {
                $commands = array_merge($commands, $provider->getCommands());
            }
        }

        return $commands;
    }
}
