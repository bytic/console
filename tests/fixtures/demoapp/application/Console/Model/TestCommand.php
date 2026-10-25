<?php

declare(strict_types=1);

namespace App\Console\Model;

use ByTIC\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Class TestCommand
 */
#[AsCommand(name: 'app:test', description: 'Test fixture command')]
class TestCommand extends Command
{
    public function handle(): int
    {
        $this->info('Fixture command');

        return self::SUCCESS;
    }
}
