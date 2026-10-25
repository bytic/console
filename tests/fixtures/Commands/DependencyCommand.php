<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Fixtures\Commands;

use ByTIC\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'test:dependency', description: 'An injected command', aliases: ['test:alias'])]
class DependencyCommand extends Command
{
    public static int $constructions = 0;

    public function __construct(private readonly Dependency $dependency)
    {
        ++self::$constructions;
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info($this->dependency->message);

        return self::FAILURE;
    }
}
