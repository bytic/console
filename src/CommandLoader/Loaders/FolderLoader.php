<?php

namespace ByTIC\Console\CommandLoader\Loaders;

use ByTIC\Console\Command;
use Nip\Utility\Arr;
use Nip\Utility\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * Class FolderLoader
 * @package ByTIC\Console\CommandLoader\Loaders
 */
class FolderLoader extends AbstractLoader
{
    /**
     * @var string[]
     */
    protected $folders = null;

    /**
     * @return string[]
     */
    public function getFolders(): array
    {
        if ($this->folders === null) {
            $this->initFolders();
        }
        return $this->folders;
    }

    /**
     * @param string[] $folders
     */
    public function setFolders(array $folders): void
    {
        $this->folders = $folders;
    }

    protected function initFolders()
    {
        $basePath = $this->getContainer()->get('path');
        $folders = [];
        $folders[] = $basePath . DIRECTORY_SEPARATOR . 'Console';
        $this->setFolders($folders);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function generateCommands(): array
    {
        $paths = array_unique(Arr::wrap($this->getFolders()));
        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return [];
        }
        return $this->loadCommandsFromPaths($paths);
    }

    /**
     * @param $paths
     * @return array
     * @throws \ReflectionException
     */
    protected function loadCommandsFromPaths($paths)
    {
        $commands = [];
        $namespace = $this->getContainer()->get('app')->getRootNamespace();
        $basePath = $this->getContainer()->get('path');

        foreach ((new Finder)->in($paths)->files() as $command) {
            /** @var \SplFileInfo $command */
            $command = $namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($command->getPathname(), realpath($basePath) . DIRECTORY_SEPARATOR)
                );

            if (is_subclass_of($command, Command::class) &&
                !(new ReflectionClass($command))->isAbstract()) {
                $commands[] = $command;
            }
        }
        return $commands;
    }
}
