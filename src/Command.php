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
    use Traits\InteractsWithIO;

    /**
     * Run the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->io = new OutputStyle($input, $output);

        return parent::run($this->input = $input, $output);
    }

    /**
     * Execute the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!method_exists($this, 'handle')) {
            throw new LogicException('You must override the execute() method in the concrete command class or write a handle method.');
        }
        return (int)call_user_func([$this, 'handle']);
    }
}
