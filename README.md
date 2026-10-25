# ByTIC Console

ByTIC-first console applications built on Symfony Console, with container-backed
command discovery and convenient input/output helpers.

## Requirements

- PHP 8.2 or newer within PHP 8.
- Symfony Console, Process, and Finder 6.x, 7.x, or 8.x.
- Symfony 8.0 requires PHP 8.4; later Symfony releases may require a newer patch.
- Composer 2 and the runtime dependencies declared in `composer.json`.

ByTIC application, container, dispatcher, router, and staging packages are
development dependencies of this library. Applications using ByTIC integration
must require those packages in their own `composer.json`; they are not installed
by `composer require bytic/console` alone.

Install with `composer require bytic/console:^2.0`. Until the stable release is
published, use the appropriate prerelease version explicitly.

The supported constraints begin at Symfony 6.0 and 7.0. Prefer maintained,
security-patched versions (6.4, 7.4, or current 8.x). The CI template exercises older Console
APIs with patched Process minors because archived Process branches have known
vulnerabilities. It does not disable Composer's advisory blocking.

Symfony components do not have to share a major version. For example, Console 8
can run alongside the Symfony DependencyInjection, HTTP, and Routing 7 components
required by current ByTIC releases. This package does not claim to migrate those
other ByTIC packages to Symfony 8.

With current ByTIC releases, the highest resolvable Console branch is 8.0:
Console 8.1 conflicts with the DependencyInjection versions allowed by
`bytic/container`. Process and Finder 8.1 can be used independently. Composer
will enforce these upstream restrictions; adopting Console 8.1 requires an
upstream ByTIC container update.

## Commands

```php
<?php

declare(strict_types=1);

namespace App\Console;

use ByTIC\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:greet', description: 'Print a greeting')]
final class GreetCommand extends Command
{
    public function handle(): int
    {
        $this->info('Hello!');

        return self::SUCCESS;
    }
}
```

Commands may instead override Symfony's
`execute(InputInterface $input, OutputInterface $output): int`. Legacy void
`handle()` methods remain successful; other non-integer return values are errors.

Use `argument()`, `option()`, `ask()`, `confirm()`, `choice()`, `secret()`, and
`table()` for input/output. Tables accept arrays or iterable rows. Output helpers
accept numeric or named verbosity levels (`v`, `vv`, `vvv`, `quiet`, `normal`).
The `silent` level requires Symfony Console 7.2+.

## Startup and discovery

Run `vendor/bin/bytic` from a Composer-installed application. Its
`bootstrap/app.php` must return a `Nip\Application\Application`. The executable
locates the consumer's autoloader through Composer's binary proxy, independently
of the current working directory.

Startup initializes the application container, calls `bootstrap()`, registers
console services, and creates a console request/router context using `app.url`
(default: `http://localhost`). It does not run HTTP dispatch/setup hooks.
Without an application bootstrap file, built-in commands are still available.
An invalid bootstrap or URL fails with a diagnostic and nonzero exit status.

`ConsoleServiceProvider` provides shared `Application::class` and
`CommandLoader::class` services, also available as `console.application` and
`console.command-loader`. Existing explicitly registered class services are
preserved.

By default, commands are discovered from service providers' `getCommands()`
results and the application's `Console` directory. Both ByTIC and ordinary
Symfony command subclasses are supported. Register constructor dependencies in
the ByTIC container; command classes are resolved through that container.

Classes with `#[AsCommand]` are wrapped in Symfony's `LazyCommand`: metadata
lookups and ordinary listings do not instantiate them. Commands overriding
`isEnabled()` are instantiated when Symfony checks their availability.
Classes configured only through `configure()` and factories without metadata
are resolved eagerly. For explicit lazy factories, register a Symfony
`LazyCommand` instance rather than inventing a separate metadata format.

Names and aliases must be non-empty and unique. Ambiguous registrations fail
instead of silently replacing commands. Instances and zero-argument closures
returning commands are also accepted by provider discovery.

For other namespace layouts:

```php
$folders = new \ByTIC\Console\CommandLoader\Loaders\FolderLoader();
$folders->setNamespaces([$directory => 'Acme\\Commands']);
$loader->setLoaders([$folders]);
```

Missing conventional directories are ignored; explicitly configured nonexistent
directories fail. Discovery scans PHP files deterministically and reports files
that do not match their configured autoload namespace.

Discovery and instances are cached. After changing folders, providers, or
registrations, call the aggregate loader's `reset()`. This clears registered
commands and discovery caches; re-add any manually registered commands.
Changing the container also invalidates loaded commands.
Configure/reset discovery before running the application; if Symfony has already
cached commands in a running application, create a new application for the new
registration set.

## Calling other commands

`call()` and `callSilent()` accept a registered name, command class, or command
instance and return its integer exit status. Classes resolve through the same
container; named calls require an attached console application. Nested calls
inherit interaction mode and share output verbosity; silent calls use a
`NullOutput`.

Binary detection returns raw paths. Prefer argument arrays for subprocesses:

```php
$process = new \Symfony\Component\Process\Process([
    \ByTIC\Console\Application::phpBinary(),
    \ByTIC\Console\Application::byticBinary(),
    'app:greet',
]);
$process->mustRun();
```

`formatCommandString()` remains available for shell integration and quotes the
PHP and console paths. Its command-string argument is shell syntax and must not
contain unescaped untrusted values.

## Development

```sh
composer install
composer test
composer analyse
composer format:check
composer audit
php tests/production-smoke.php
```

`composer format` applies PER Coding Style 2.0 and strict types. PHPStan checks
runtime code at level 5 without a baseline. Narrow vendor stubs document the
ByTIC container's dynamic `hasDefinition()` method and correct the HTTP request
singleton's vendor PHPDoc.

The production smoke check installs a separate consumer with `--no-dev`,
executes its real Composer binary proxy and application command from another
directory, checks startup failure behavior, and removes its temporary files.
Use `CONSOLE_VERSION`, `PROCESS_VERSION`, and `FINDER_VERSION` to constrain that
consumer (defaults: `^8.0`; on PHP 8.2/8.3 select `^6.4` or `^7.4`).

### Activating CI

The GitHub Actions configuration is provided as an **inactive template** at
`.github/ci.yml.dist`. To enable it, copy it to `.github/workflows/ci.yml` and
commit that file using credentials with workflow-write permission:

```sh
mkdir -p .github/workflows
cp .github/ci.yml.dist .github/workflows/ci.yml
```

The template covers PHP 8.2-8.5, Console 6.0/6.4/7.0/7.4/8.0, compatible mixed
7/8 component versions, secure lowest dependencies, production installs, and
quality checks. It does not run until activated.

See [UPGRADE-2.0.md](UPGRADE-2.0.md) for migration details.
