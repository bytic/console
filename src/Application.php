<?php

declare(strict_types=1);

namespace ByTIC\Console;

use ByTIC\Console\Application\DetectBinary;
use ByTIC\Console\Application\HasBootstrappers;
use ByTIC\Console\Support\ProcessUtils;
use Nip\Container\ContainerAwareTrait;
use Symfony\Component\Console\Application as SymfonyApplication;
use Composer\InstalledVersions;

/**
 * Class Application
 */
class Application extends SymfonyApplication
{
    use ContainerAwareTrait;
    use HasBootstrappers;
    use DetectBinary;

    /**
     * @inheritDoc
     */
    public function __construct(string $name = 'BYTIC Console', ?string $version = null)
    {
        $version ??= InstalledVersions::isInstalled('bytic/console')
            ? InstalledVersions::getPrettyVersion('bytic/console')
            : '2.0-dev';
        parent::__construct($name, $version ?? '2.0-dev');
        $this->bootstrap();
    }

    /**
     * Format the given command as a fully-qualified executable command.
     *
     * @param  string  $string
     * @return string
     */
    public static function formatCommandString(string $string): string
    {
        return sprintf('%s %s %s', ProcessUtils::escapeArgument(static::phpBinary()), ProcessUtils::escapeArgument(static::byticBinary()), $string);
    }
}
