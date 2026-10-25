<?php

declare(strict_types=1);

namespace ByTIC\Console\Application;

use Closure;

/**
 * Trait HasBootstrappers
 */
trait HasBootstrappers
{
    /**
     * The console application bootstrappers.
     *
     * @var array
     */
    protected static array $bootstrappers = [];

    /**
     * Register a console "starting" bootstrapper.
     *
     * @param Closure $callback
     * @return void
     */
    public static function starting(Closure $callback): void
    {
        static::$bootstrappers[] = $callback;
    }

    public static function clearBootstrappers(): void
    {
        static::$bootstrappers = [];
    }

    /**
     * Bootstrap the console application.
     *
     * @return void
     */
    protected function bootstrap(): void
    {
        foreach (static::$bootstrappers as $bootstrapper) {
            $bootstrapper($this);
        }
    }
}
