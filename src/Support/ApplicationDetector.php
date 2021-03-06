<?php

namespace ByTIC\Console\Support;

use Exception;
use Nip\Application\Application;
use Nip\Container\Container;

/**
 * Class ApplicationDetector
 * @package ByTIC\Console\Support
 */
class ApplicationDetector
{
    protected $basePath = '';

    /**
     * ApplicationDetector constructor.
     * @param string $basePath
     * @throws Exception
     */
    public function __construct(string $basePath = null)
    {
        if ($basePath === null) {
            $basePath = defined('BYTIC_CONSOLE_ROOT_DIR') ? BYTIC_CONSOLE_ROOT_DIR : null;
        }
        if (!is_dir($basePath)) {
            throw new Exception("BasePath in " . __CLASS__ . ' cannot be null or invalid directory [' . $basePath . ']');
        }
        $this->basePath = $basePath;
    }

    /**
     * @return Container|boolean
     */
    public function getContainer()
    {
        $app = $this->getBootstrapApp();
        if ($app === false) {
            return false;
        }
        $this->initBootstrapApp($app);
        return $app->getContainer();
    }

    /**
     * @param Application $app
     */
    protected function initBootstrapApp($app)
    {
        $app->bootstrap();
        if (method_exists($app, 'setupRequest')) {
            $app->setupRequest();
        }
        if (method_exists($app, 'setup')) {
            $app->setup();
        }
        if (method_exists($app, 'preHandleRequest')) {
            $app->preHandleRequest();
        }
        if (method_exists($app, 'preRouting')) {
            $app->preRouting();
        }
        \ByTIC\Console\CommandLoader\Loaders\ServiceProvidersLoader::setProvidersFromApplication($app);
//        $app->registerConfiguredProviders();
    }

    /**
     * @return mixed
     */
    protected function getBootstrapApp()
    {
        $appFile = $this->initAppFile();
        if ($appFile === false) {
            return false;
        }

        /** @noinspection PhpIncludeInspection */
        $app = require_once $appFile;
        return $app;
    }

    /**
     * @return bool|string
     */
    protected function initAppFile()
    {
        $baseDir = realpath($this->basePath);

        if ($baseDir == dirname(dirname(__DIR__))) {
            return false;
        }
        $appFile = realpath($baseDir . '/bootstrap/app.php');
        if (!file_exists($appFile)) {
            return false;
        }
        return $appFile;
    }
}