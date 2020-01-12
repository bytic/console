<?php

namespace ByTIC\Console;

use ByTIC\Console\Application\DetectBinary;
use ByTIC\Console\Application\HasBootstrappers;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Class Application
 * @package ByTIC\Console
 */
class Application extends SymfonyApplication
{
    use HasBootstrappers;
    use DetectBinary;

    /**
     * @inheritDoc
     */
    public function __construct($name = 'BYTIC Console', $version = 'v1.0.0')
    {
        parent::__construct($name, $version);
        $this->bootstrap();
    }

    /**
     * Format the given command as a fully-qualified executable command.
     *
     * @param  string  $string
     * @return string
     */
    public static function formatCommandString($string)
    {
        return sprintf('%s %s %s', static::phpBinary(), static::byticBinary(), $string);
    }
}
