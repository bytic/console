<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Support;

use ByTIC\Console\Application;
use ByTIC\Console\Support\ProcessUtils;
use ByTIC\Console\Tests\AbstractTest;
use Symfony\Component\Process\Process;

class ProcessUtilsTest extends AbstractTest
{
    public function testShellArgumentsRoundTrip(): void
    {
        foreach (['', 'with spaces', "with'quote", 'with"quote', 'trailing\\', '%PATH%'] as $argument) {
            $command = ProcessUtils::escapeArgument(PHP_BINARY)
                . ' -r ' . ProcessUtils::escapeArgument('echo $argv[1];')
                . ' ' . ProcessUtils::escapeArgument($argument);
            $process = Process::fromShellCommandline($command);
            $process->mustRun();
            self::assertSame($argument, $process->getOutput());
        }
    }

    public function testBinaryPathsAreRawAndFormattingIncludesPhp(): void
    {
        self::assertFileExists(Application::phpBinary());
        self::assertFileExists(Application::byticBinary());
        self::assertSame(
            ProcessUtils::escapeArgument(Application::phpBinary()) . ' '
                . ProcessUtils::escapeArgument(Application::byticBinary()) . ' list',
            Application::formatCommandString('list'),
        );
    }
}
