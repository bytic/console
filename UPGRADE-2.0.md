# Upgrading to 2.0

## Platform and dependencies

PHP 8.2 and Composer 2 are required. Symfony 5 support has been removed.
Console, Process, and Finder accept `^6.0 || ^7.0 || ^8.0`; Symfony's own PHP
requirements determine which versions Composer can install. Use security-patched
releases, not archived vulnerable Process minors.

ByTIC application, container, dispatcher, router, and staging dependencies
remain development-only, with their supported major ranges updated to 1.x/2.x.
Applications using the ByTIC integration must declare the required packages in
their own `composer.json`. Runtime requirements still include the HTTP, config,
and utility components used by the package's production integration.
Compatible mixtures of Symfony 7 and 8 components are supported; a
completely Symfony-8-based ByTIC runtime needs separate upstream dependency
updates.
Console 8.1 specifically conflicts with the DI versions currently allowed by
`bytic/container`, so Console 8.0 is the latest resolvable branch until that
upstream package is updated. Process/Finder 8.1 remain independently usable.

PSR HTTP Message versions below 1.1 are excluded because the current ByTIC HTTP
bridge implements the typed `withUri()` parameter introduced in 1.1. Allowing
1.0 would produce a fatal interface-signature error on a lowest-dependency install.

## Command APIs

- Update overridden methods/properties to match the new native types. All PHP
  source uses strict types. Output strings must be strings, and helper verbosity
  accepts `string|int|null`.
- Prefer `handle(): int` and Symfony's `SUCCESS`, `FAILURE`, and `INVALID`
  constants. Void/null still means success; strings, booleans, and objects no
  longer get silently cast to an exit status.
- `execute()` overrides remain supported. Newer invokable-command and input
  attributes are not required, preserving Symfony 6 compatibility.
- Replace `$defaultName`/`$defaultDescription` or static default-name methods with
  `#[AsCommand]` or `configure()`. Symfony 7/8 removed those legacy APIs.
- `table()` accepts iterable arrays of cells, not an undeclared Illuminate
  `Arrayable` contract. Convert such objects with `toArray()` explicitly.
- `setIo()` accepts `SymfonyStyle`; `$output` now refers to raw output and `$io`
  to the single style instance. `choice()` can return an array for multiselect.

## Dependency injection and discovery

Commands no longer implicitly fetch the global container. Resolve them through
the configured loader/application or call `setContainer()` explicitly in tests.
Class-name registrations and nested calls use container resolution, supporting
constructor injection.

Providers are instance-scoped. Replace static
`ServiceProvidersLoader::setProviders()` or `setProvidersFromApplication()` calls
with a configured loader instance's `setProviders($providers)` or automatic
discovery from its container's provider repository.

The conventional application `Console` directory remains supported. Configure
explicit directory-to-namespace mappings using `setNamespaces()` for other
layouts. Explicit invalid directories and namespace/autoload mismatches now
throw useful exceptions.

Names and aliases must be unique. Remove duplicate registrations instead of
relying on last-write-wins. `setCommands()` now derives names/aliases from command
instances rather than trusting array keys. Pass an array to `resolveCommands()`;
the old undocumented variadic fallback is removed.

Attribute-backed classes are lazy unless an overridden `isEnabled()` needs to be
evaluated. Commands that derive metadata only in `configure()` are eager.
Call the aggregate loader's `reset()` after changing discovered registrations.
Reset and container changes discard manually registered commands too.

`Application::starting()` remains a process-wide legacy callback registry.
Use `clearBootstrappers()` for isolation in tests or long-running processes.
Prefer configuring individual applications where global callbacks are unnecessary.

## Startup

Application bootstrap files must return a ByTIC application. Missing bootstrap
files permit built-in console use; malformed files fail explicitly. The detector
returns `null`, not `false`, when no application exists and caches the validated
container per detector instance.

Startup initializes and synchronizes the application's container and path service
before bootstrapping. It calls `bootstrap()` but no longer invokes `setupRequest`,
`setup`, `preHandleRequest`, or `preRouting`. Move required non-HTTP initialization
into application bootstrappers/providers. Request/router context initialization
remains available for console URL generation using the supplied container.

Uncaught startup errors now write to stderr and exit with status 1. Command
errors retain Symfony's rendering and status handling.

The default application version comes from Composer's installed package metadata,
with `2.0-dev` when metadata is unavailable; it is no longer hardcoded to 1.0.

## Binary and process helpers

`phpBinary()` and `byticBinary()` now return unquoted paths. The latter selects an
installed proxy when available and otherwise the package executable. Remove
caller assumptions that those values are already escaped.

Prefer Symfony Process argument arrays. `formatCommandString()` now includes
both the escaped PHP executable and escaped console path. Its remaining string
is shell syntax, not a safe place to interpolate user input.

`ProcessUtils` is now a final package-owned quoting helper, not a subclass of
Symfony's utility class. Do not use inherited Symfony methods through it.

## Development tooling

PHPUnit 11.5, Mockery, PHPStan 2, and PHP-CS-Fixer are direct development
dependencies. The old QA bundle/plugin permissions and Travis/SensioLabs/
Scrutinizer/StyleCI configurations have been replaced by Composer scripts and an
inactive GitHub Actions template at `.github/ci.yml.dist`. Activate it by copying
it to `.github/workflows/ci.yml` using workflow-authorized credentials; see the
README. Run `composer test`, `composer analyse`, and
`composer format:check`. Update local PHPUnit configuration to the new schema.
