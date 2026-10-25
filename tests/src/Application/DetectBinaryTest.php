<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Application;

use ByTIC\Console\Application;
use ByTIC\Console\Tests\AbstractTest;

/**
 * Class DetectBinaryTest
 */
class DetectBinaryTest extends AbstractTest
{
    public function test_byticBinary_finder()
    {
        $path = Application::byticBinary();

        self::assertFileExists($path);
        self::assertStringEndsWith('bin/bytic', $path);
    }
}
