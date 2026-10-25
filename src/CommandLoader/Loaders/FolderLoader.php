<?php

declare(strict_types=1);

namespace ByTIC\Console\CommandLoader\Loaders;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

class FolderLoader extends AbstractLoader
{
    /** @var list<string>|null */
    protected ?array $folders = null;

    /** @var array<string, string> */
    protected array $namespaces = [];

    /** @return list<string> */
    public function getFolders(): array
    {
        if ($this->folders !== null) {
            return $this->folders;
        }
        $container = $this->getContainer();

        return $container->has('path')
            ? [$container->get('path') . DIRECTORY_SEPARATOR . 'Console']
            : [];
    }

    /** @param list<string> $folders */
    public function setFolders(array $folders): void
    {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                throw new \InvalidArgumentException(sprintf('Command directory "%s" does not exist.', $folder));
            }
        }
        $this->folders = $folders;
        $this->namespaces = [];
        $this->reset();
    }

    /** @param array<string, string> $namespaces Directory => namespace prefix. */
    public function setNamespaces(array $namespaces): void
    {
        $this->setFolders(array_keys($namespaces));
        $this->namespaces = $namespaces;
    }

    protected function generateCommands(): array
    {
        $commands = [];
        foreach (array_unique($this->getFolders()) as $folder) {
            if (!is_dir($folder)) {
                continue;
            }

            $base = realpath($folder);
            $namespace = $this->namespaces[$folder] ?? null;
            if ($namespace === null) {
                $container = $this->getContainer();
                if (!$container->has('path') || !$container->has('app')) {
                    throw new \LogicException('Folder discovery requires path/app services or explicit namespace mappings.');
                }
                $base = realpath((string) $container->get('path'));
                $namespace = $container->get('app')->getRootNamespace();
            }
            if ($base === false) {
                throw new \LogicException(sprintf('Cannot resolve command base directory for "%s".', $folder));
            }

            foreach ((new Finder())->in($folder)->files()->name('*.php')->sortByName() as $file) {
                $path = $file->getRealPath();
                if ($path === false || !str_starts_with($path, $base . DIRECTORY_SEPARATOR)) {
                    throw new \LogicException(sprintf('Command file "%s" is outside its namespace directory.', $file->getPathname()));
                }
                $relative = substr($path, strlen($base) + 1, -4);
                $class = rtrim($namespace, '\\') . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
                if (!class_exists($class)) {
                    throw new \LogicException(sprintf('Command file "%s" does not autoload as "%s".', $path, $class));
                }
                if (is_subclass_of($class, Command::class) && !(new ReflectionClass($class))->isAbstract()) {
                    $commands[] = $class;
                }
            }
        }

        return $commands;
    }
}
