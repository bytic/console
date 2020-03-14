<?php

namespace ByTIC\Console\Tests\Application;

use ByTIC\Console\Application;
use ByTIC\Console\Tests\AbstractTest;

/**
 * Class DetectBinaryTest
 * @package ByTIC\Console\Tests\Application
 */
class DetectBinaryTest extends AbstractTest
{
    public function test_byticBinary_finder()
    {
        $path = Application::byticBinary();

        self::assertStringEndsWith('vendor\bin\bytic', $path);
    }
}