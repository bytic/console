<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Support;

use ByTIC\Console\Support\SetRequestForConsole;
use ByTIC\Console\Tests\AbstractTest;
use Nip\Config\Config;
use Nip\Container\Container;

class SetRequestForConsoleTest extends AbstractTest
{
    public function testRequestUsesSuppliedContainerRatherThanGlobalConfig(): void
    {
        $container = new Container();
        $container->set('config', new Config(['app' => ['url' => 'https://console.example/base']]));
        SetRequestForConsole::bootstrap($container);
        self::assertSame('console.example', $container->get('request')->getHost());
        self::assertSame('/base', $container->get('request')->getBaseUrl());
    }

    public function testMissingConfigUsesLocalhost(): void
    {
        $container = new Container();
        SetRequestForConsole::bootstrap($container);
        self::assertSame('localhost', $container->get('request')->getHost());
    }

    public function testInvalidUrlFailsExplicitly(): void
    {
        $container = new Container();
        $container->set('config', new Config(['app' => ['url' => 'not a url']]));
        $this->expectException(\InvalidArgumentException::class);
        SetRequestForConsole::bootstrap($container);
    }
}
