<?php

namespace ByTIC\Console\Tests;

class CommandTest extends AbstractTest
{
    public function test_load()
    {
        exec('php ' . PROJECT_BASE_PATH . '/bin/bytic', $output, $return);

        self::assertIsArray($output);
        self::assertStringStartsWith('BYTIC Console', $output[0]);
    }
}