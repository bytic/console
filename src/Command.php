<?php

declare(strict_types=1);

namespace ByTIC\Console;

use ByTIC\Console\Output\OutputStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 *
 * @method mixed handle()
 */
abstract class Command extends SymfonyCommand
{
    use Traits\CallsCommands;
    use Traits\HasApplication;
    use Traits\InteractsWithIO;

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->io = new OutputStyle($input, $output);

        return parent::run($this->input = $input, $output);
    }

    /**
     * @inheritDoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!method_exists($this, 'handle')) {
            throw new LogicException('You must override the execute() method in the concrete command class or write a handle method.');
        }
        $result = $this->handle();
        if ($result !== null && !is_int($result)) {
            throw new LogicException('Command handle() must return an integer exit status or null.');
        }

        return $result ?? self::SUCCESS;
    }

    /**
     * @param mixed $command
     */
    protected function resolveCommand(mixed $command): SymfonyCommand
    {
        if ($command instanceof SymfonyCommand) {
            $resolved = $command;
        } elseif (is_string($command) && is_a($command, SymfonyCommand::class, true)) {
            $resolved = $this->getContainer()->get($command);
        } elseif (is_string($command)) {
            $application = $this->getApplication();
            if ($application === null) {
                throw new LogicException('Calling a named command requires a console application.');
            }
            $resolved = $application->find($command);
        } else {
            throw new LogicException('Expected a Symfony command instance, class name, or registered name.');
        }

        if (!$resolved instanceof SymfonyCommand) {
            throw new LogicException('The container did not resolve a Symfony command.');
        }
        $resolved->setApplication($this->getApplication());
        if ($resolved instanceof self) {
            $resolved->setContainer($this->getContainer());
        }

        return $resolved;
    }

}
