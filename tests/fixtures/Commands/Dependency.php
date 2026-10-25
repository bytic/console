<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Fixtures\Commands;

final class Dependency
{
    public function __construct(public readonly string $message = 'injected') {}
}
