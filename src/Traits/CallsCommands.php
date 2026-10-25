<?php

declare(strict_types=1);

namespace ByTIC\Console\Traits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait CallsCommands
 *
 * @property OutputInterface $output
 */
trait CallsCommands
{
    /**
     * Resolve the console command instance for the given command.
     *
     * @param Command|string $command
     * @return Command
     */
    abstract protected function resolveCommand(mixed $command): Command;

    /**
     * Call another console command.
     *
     * @param Command|string $command
     * @param array $arguments
     * @return int
     */
    public function call(Command|string $command, array $arguments = []): int
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
    public function callSilent(Command|string $command, array $arguments = []): int
    {
        return $this->runCommand($command, $arguments, new NullOutput());
    }

    /**
     * Run the given the console command.
     *
     * @param Command|string $command
     * @param array $arguments
     * @param OutputInterface $output
     * @return int
     */
    protected function runCommand(Command|string $command, array $arguments, OutputInterface $output): int
    {
        $command = $this->resolveCommand($command);
        if ($command->getApplication() !== null) {
            $arguments['command'] = $command->getName();
        }

        return $command->run(
            $this->createInputFromArguments($arguments),
            $output,
        );
    }

    /**
     * Create an input instance from the given arguments.
     *
     * @param array $arguments
     * @return \Symfony\Component\Console\Input\ArrayInput
     */
    protected function createInputFromArguments(array $arguments): ArrayInput
    {
        $input = new ArrayInput(array_merge($this->context(), $arguments));
        $input->setInteractive($this->input->isInteractive());

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
    protected function context(): array
    {
        return [];
    }
}
