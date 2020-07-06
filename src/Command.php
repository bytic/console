<?php

namespace ByTIC\Console;

use ByTIC\Console\Output\OutputStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package ByTIC\Console
 */
abstract class Command extends SymfonyCommand
{
    use Traits\CallsCommands;
    use Traits\HasApplication;
    use Traits\InteractsWithIO;

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->output = new OutputStyle($input, $output);
        $this->io = new OutputStyle($input, $output);

        return parent::run($this->input = $input, $output);
    }

    /**
     * @inheritDoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!method_exists($this, 'handle')) {
            throw new LogicException('You must override the execute() method in the concrete command class or write a handle method.');
        }
        return (int)call_user_func([$this, 'handle']);
    }


    /**
     * @inheritDoc
     */
    protected function resolveCommand($command)
    {
        if (!class_exists($command) && is_string($command)) {
            return $this->getApplication()->find($command);
        }

        $command = new $command();

        if ($command instanceof SymfonyCommand) {
            $command->setApplication($this->getApplication());
        }

//        if ($command instanceof self) {
//            $command->setLaravel($this->getLaravel());
//        }

        return $command;
    }
}
