<?php

declare(strict_types=1);

namespace ByTIC\Console\Support;

use Nip\Container\Container;
use Nip\Http\Request;
use Nip\Router\RequestContext;

class SetRequestForConsole
{
    public static function bootstrap(Container $container): void
    {
        $uri = static::detectUrl($container);
        $components = parse_url($uri);
        if ($components === false || !isset($components['scheme'], $components['host'])) {
            throw new \InvalidArgumentException(sprintf('Invalid console application URL "%s".', $uri));
        }

        $server = $_SERVER;
        if (isset($components['path'])) {
            $server['SCRIPT_FILENAME'] = $components['path'];
            $server['SCRIPT_NAME'] = $components['path'];
        }
        $request = Request::create($uri, 'GET', [], [], [], $server);
        Request::instance($request);
        $container->set('request', $request);

        if ($container->has('router')) {
            $context = (new RequestContext())->fromRequest($request);
            $container->get('router')->setContext($context);
        }
    }

    protected static function detectUrl(Container $container): string
    {
        if (!$container->has('config')) {
            return 'http://localhost';
        }

        return $container->get('config')->get('app.url', 'http://localhost');
    }
}
