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
}
