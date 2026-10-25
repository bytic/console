<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests;

/**
 * Class CommandTest
 */
class CommandTest extends AbstractTest
{
    public function test_load()
    {
        $process = new \Symfony\Component\Process\Process([PHP_BINARY, PROJECT_BASE_PATH . '/bin/bytic'], sys_get_temp_dir());
        $process->run();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringStartsWith('BYTIC Console', $process->getOutput());
    }
}
