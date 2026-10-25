<?php

declare(strict_types=1);

namespace ByTIC\Console\Traits;

use Nip\Application\Application;
use Nip\Container\Container;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Trait HasApplication
 */
trait HasApplication
{
    protected ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container !== null) {
            return $this->container;
        }
        $application = $this->getApplication();
        if ($application instanceof \ByTIC\Console\Application && $application->hasContainer()) {
            return $application->getContainer();
        }

        throw new LogicException('No container configured for this command.');
    }

    /**
     * @param null|string $name
     * @return mixed|Container|object
     */
    protected function getFromContainer(?string $name = null): mixed
    {
        if (empty($name)) {
            return $this->getContainer();
        }

        return $this->getContainer()->get($name);
    }

    /**
     * @return Application
     */
    protected function getByticApp(): Application
    {
        $application = $this->getFromContainer('app');
        if (!$application instanceof Application) {
            throw new LogicException('The app service must be a ByTIC application.');
        }

        return $application;
    }
}
