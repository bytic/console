<?php

declare(strict_types=1);

namespace ByTIC\Console\Traits;

use Nip\Utility\Str;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Trait InteractsWithIO
 */
trait InteractsWithIO
{
    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected InputInterface $input;

    /**
     * The output interface implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected OutputInterface $output;

    /**
     * The output interface implementation.
     *
     * @var SymfonyStyle
     */
    protected SymfonyStyle $io;

    /**
     * The default verbosity of output commands.
     *
     * @var int
     */
    protected int $verbosity = OutputInterface::VERBOSITY_NORMAL;

    /**
     * The mapping between human readable verbosity levels and Symfony's OutputInterface.
     *
     * @var array
     */
    protected array $verbosityMap = [
        'v' => OutputInterface::VERBOSITY_VERBOSE,
        'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'vvv' => OutputInterface::VERBOSITY_DEBUG,
        'quiet' => OutputInterface::VERBOSITY_QUIET,
        'normal' => OutputInterface::VERBOSITY_NORMAL,
    ];

    /**
     * Determine if the given argument is present.
     *
     * @param string $name
     * @return bool
     */
    public function hasArgument(string $name): bool
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get the value of a command argument.
     *
     * @param string|null $key
     * @return string|array|null
     */
    public function argument(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * Get all of the arguments passed to the command.
     *
     * @return array
     */
    public function arguments(): array
    {
        return $this->argument();
    }

    /**
     * Determine if the given option is present.
     *
     * @param string $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return $this->input->hasOption($name);
    }

    /**
     * Get the value of a command option.
     *
     * @param string|null $key
     * @return string|array|bool|null
     */
    public function option(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * Get all of the options passed to the command.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->option();
    }

    /**
     * Confirm a question with the user.
     *
     * @param string $question
     * @param bool $default
     * @return bool
     */
    public function confirm(string $question, bool $default = false): bool
    {
        return $this->io->confirm($question, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param string $question
     * @param string|null $default
     * @return mixed
     */
    public function ask(string $question, ?string $default = null): mixed
    {
        return $this->io->ask($question, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param string $question
     * @param array|callable $choices
     * @param string|null $default
     * @return mixed
     */
    public function anticipate(string $question, array|callable $choices, ?string $default = null): mixed
    {
        return $this->askWithCompletion($question, $choices, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param string $question
     * @param array|callable $choices
     * @param string|null $default
     * @return mixed
     */
    public function askWithCompletion(string $question, array|callable $choices, ?string $default = null): mixed
    {
        $question = new Question($question, $default);

        is_callable($choices)
            ? $question->setAutocompleterCallback($choices)
            : $question->setAutocompleterValues($choices);

        return $this->io->askQuestion($question);
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param string $question
     * @param bool $fallback
     * @return mixed
     */
    public function secret(string $question, bool $fallback = true): mixed
    {
        $question = new Question($question);

        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->io->askQuestion($question);
    }

    /**
     * Give the user a single choice from an array of answers.
     *
     * @param string $question
     * @param array $choices
     * @param string|null $default
     * @param int|null $attempts
     * @param bool $multiple
     * @return string|array
     */
    public function choice(string $question, array $choices, string|int|null $default = null, ?int $attempts = null, bool $multiple = false): string|array
    {
        $question = new ChoiceQuestion($question, $choices, $default);

        $question->setMaxAttempts($attempts)->setMultiselect($multiple);

        return $this->io->askQuestion($question);
    }

    /**
     * Format input to textual table.
     *
     * @param array $headers
     * @param iterable<array> $rows
     * @param string $tableStyle
     * @param array $columnStyles
     * @return void
     */
    public function table($headers, iterable $rows, $tableStyle = 'default', array $columnStyles = []): void
    {
        $table = new Table($this->io);

        $rows = is_array($rows) ? $rows : iterator_to_array($rows, false);

        $table->setHeaders((array) $headers)->setRows($rows)->setStyle($tableStyle);

        foreach ($columnStyles as $columnIndex => $columnStyle) {
            $table->setColumnStyle($columnIndex, $columnStyle);
        }

        $table->render();
    }

    /**
     * Write a string as information output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function info(string $string, string|int|null $verbosity = null): void
    {
        $this->line($string, 'info', $verbosity);
    }

    /**
     * Write a string as standard output.
     *
     * @param string $string
     * @param string|null $style
     * @param int|string|null $verbosity
     * @return void
     */
    public function line(string $string, ?string $style = null, string|int|null $verbosity = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->io->writeln($styled, $this->parseVerbosity($verbosity));
    }

    /**
     * Write a string as comment output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function comment(string $string, string|int|null $verbosity = null): void
    {
        $this->line($string, 'comment', $verbosity);
    }

    /**
     * Write a string as question output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function question(string $string, string|int|null $verbosity = null): void
    {
        $this->line($string, 'question', $verbosity);
    }

    /**
     * Write a string as error output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function error(string $string, string|int|null $verbosity = null): void
    {
        $this->line($string, 'error', $verbosity);
    }

    /**
     * Write a string as warning output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function warn(string $string, string|int|null $verbosity = null): void
    {
        if (!$this->io->getFormatter()->hasStyle('warning')) {
            $style = new OutputFormatterStyle('yellow');

            $this->io->getFormatter()->setStyle('warning', $style);
        }

        $this->line($string, 'warning', $verbosity);
    }

    /**
     * Write a string in an alert box.
     *
     * @param string $string
     * @return void
     */
    public function alert(string $string): void
    {
        $length = Str::length(strip_tags($string)) + 12;

        $this->comment(str_repeat('*', $length));
        $this->comment('*     ' . $string . '     *');
        $this->comment(str_repeat('*', $length));

        $this->io->newLine();
    }

    /**
     * Set the input interface implementation.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return void
     */
    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * Set the output interface implementation.
     *
     * @param SymfonyStyle $io
     * @return void
     */
    public function setIo(SymfonyStyle $io): void
    {
        $this->io = $io;
    }

    /**
     * Set the verbosity level.
     *
     * @param string|int $level
     * @return void
     */
    protected function setVerbosity(string|int $level): void
    {
        $this->verbosity = $this->parseVerbosity($level);
    }

    /**
     * Get the verbosity level in terms of Symfony's OutputInterface level.
     *
     * @param string|int|null $level
     * @return int
     */
    protected function parseVerbosity(string|int|null $level = null): int
    {
        if ($level === 'silent') {
            if (!defined(OutputInterface::class . '::VERBOSITY_SILENT')) {
                throw new \InvalidArgumentException('Silent verbosity requires Symfony Console 7.2 or newer.');
            }

            return constant(OutputInterface::class . '::VERBOSITY_SILENT');
        }
        if ($level !== null && isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (!is_int($level)) {
            $level = $this->verbosity;
        }

        return $level;
    }

    /**
     * Get the output implementation.
     *
     * @return SymfonyStyle
     */
    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }
}
