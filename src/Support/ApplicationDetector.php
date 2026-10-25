<?php

declare(strict_types=1);

namespace ByTIC\Console\Support;

use Nip\Application\Application;
use Nip\Container\Container;

class ApplicationDetector
{
    protected string $basePath;
    private ?Container $container = null;

    public function __construct(?string $basePath = null)
    {
        $basePath ??= defined('BYTIC_CONSOLE_ROOT_DIR') ? BYTIC_CONSOLE_ROOT_DIR : null;
        if ($basePath === null || !is_dir($basePath)) {
            throw new \InvalidArgumentException(sprintf('Invalid console base directory "%s".', $basePath ?? ''));
        }
        $this->basePath = $basePath;
    }

    public function getContainer(): ?Container
    {
        if ($this->container !== null) {
            return $this->container;
        }
        $file = $this->initAppFile();
        if ($file === null) {
            return null;
        }
        $app = require $file;
        if (!$app instanceof Application) {
            throw new \UnexpectedValueException(sprintf('Bootstrap "%s" must return a ByTIC application.', $file));
        }

        if (!$app->hasContainer()) {
            $app->initContainer();
        }
        $container = $app->getContainer();
        if (!$container instanceof Container) {
            throw new \UnexpectedValueException('The ByTIC application must provide a ByTIC container.');
        }
        Container::setInstance($container);
        $container->set('app', $app);
        if (!$container->has('path')) {
            $container->setParameter('path', $app->path());
        }
        $app->bootstrap();
        $container->setProviderRepository($app->getProviderRepository());
        $this->container = $container;

        return $this->container;
    }

    protected function initAppFile(): ?string
    {
        $base = realpath($this->basePath);
        if ($base === dirname(__DIR__, 2)) {
            return null;
        }
        $file = $base . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';

        return is_file($file) ? $file : null;
    }
}
