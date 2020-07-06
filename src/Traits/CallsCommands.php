<?php

namespace ByTIC\Console\Traits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait CallsCommands
 * @package ByTIC\Console\Traits
 *
 * @property $output
 */
trait CallsCommands
{
    /**
     * Resolve the console command instance for the given command.
     *
     * @param Command|string $command
     * @return Command
     */
    abstract protected function resolveCommand($command);

    /**
     * Call another console command.
     *
     * @param Command|string $command
     * @param array $arguments
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        return $this->runCommand($command, $arguments, $this->output);
    }

    /**
     * Call another console command silently.
     *
     * @param Command|string $command
     * @param array $arguments
     * @return int
     */
    public function callSilent($command, array $arguments = [])
    {
        return $this->runCommand($command, $arguments, new NullOutput);
    }

    /**
     * Run the given the console command.
     *
     * @param Command|string $command
     * @param array $arguments
     * @param OutputInterface $output
     * @return int
     */
    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        $arguments['command'] = $command;

        return $this->resolveCommand($command)->run(
            $this->createInputFromArguments($arguments), $output
        );
    }

    /**
     * Create an input instance from the given arguments.
     *
     * @param array $arguments
     * @return \Symfony\Component\Console\Input\ArrayInput
     */
    protected function createInputFromArguments(array $arguments)
    {
        $input = new ArrayInput(array_merge($this->context(), $arguments));

        if ($input->hasParameterOption(['--no-interaction'], true)) {
            $input->setInteractive(false);
        }
        return $input;
    }

    /**
     * Get all of the context passed to the command.
     *
     * @return array
     */
    protected function context()
    {
        return [];
    }
}