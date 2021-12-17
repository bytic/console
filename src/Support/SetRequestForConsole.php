<?php

namespace ByTIC\Console\Support;

use Nip\Http\Request;
use Nip\Router\RequestContext;
use function config;

class SetRequestForConsole
{
    /**
     * Bootstrap the given application.
     *
     * @param $container
     * @return void
     */
    public static function bootstrap($container)
    {
        $uri = static::detectUrl();

        $components = parse_url($uri);

        $server = $_SERVER;

        if (isset($components['path'])) {
            $server = array_merge($server, [
                'SCRIPT_FILENAME' => $components['path'],
                'SCRIPT_NAME' => $components['path'],
            ]);
        }
        $request =  Request::create(
            $uri, 'GET', [], [], [], $server
        );
        Request::instance($request);
        $container->set('request',$request);

        if ($container->has('router')) {
            $context = (new RequestContext)->fromRequest($request);
            app('router')->setContext($context);
        }
    }

    protected static function detectUrl()
    {
        $default = 'http://localhost';

        $container = \Nip\Container\Container::getInstance();
        if (false === $container->has('config')) {
            return $default;
        }

        return config('app.url', $default);
    }
}
