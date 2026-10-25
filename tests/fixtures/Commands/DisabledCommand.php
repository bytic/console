<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Fixtures\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'test:disabled')]
class DisabledCommand extends Command
{
    public function isEnabled(): bool
    {
        return false;
    }
}
