# Changelog

## 2.0.0 - Unreleased

### Added

- Explicit production dependency constraints and PHP 8.2 baseline.
- An inactive CI template with Symfony Console, Finder, and Process 6/7/8 compatibility jobs, including
  compatible mixed 7/8 installations.
- Shared console services, container-backed command construction, attribute-based
  lazy discovery, aliases, namespace mappings, and discovery reset support.
- Regression coverage for handlers, nested calls, interactive helpers, discovery,
  container isolation, request context, and installed production consumers.
- PHPUnit 11.5, PHPStan 2, PER-CS 2.0 formatting, and Composer scripts.

### Changed

- Strict typing and explicit failures replace loosely typed or silently failing
  paths. Symfony 5 and PHP versions below 8.2 are no longer supported.
- Provider collections are instance-scoped; duplicate command names/aliases fail.
- Startup uses Composer's binary proxy, validates the application, binds its
  container/path, and avoids HTTP dispatch lifecycle hooks.
- Command output uses one style instance; table rows support iterables.
- Binary helpers return raw paths; shell formatting includes the PHP executable.

See [UPGRADE-2.0.md](UPGRADE-2.0.md) for breaking changes.
