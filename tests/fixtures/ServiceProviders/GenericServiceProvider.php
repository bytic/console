<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Fixtures\ServiceProviders;

/**
 * Class GenericServiceProvider
 */
class GenericServiceProvider extends \Nip\Container\ServiceProviders\Providers\AbstractServiceProvider
{
    public function registerCommands()
    {
        $this->commands(\App\Console\Model\TestCommand::class);
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        // TODO: Implement provides() method.
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}
