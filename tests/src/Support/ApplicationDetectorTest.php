<?php

namespace ByTIC\Console\Tests\Support;

use ByTIC\Console\Support\ApplicationDetector;
use ByTIC\Console\Tests\AbstractTest;
use Nip\Container\Container;

/**
 * Class ApplicationDetectorTest
 * @package ByTIC\Console\Tests\Support
 */
class ApplicationDetectorTest extends AbstractTest
{
    public function test_getContainer()
    {
        $applicationDetector = \Mockery::mock(ApplicationDetector::class, [TEST_FIXTURE_PATH . '/demoapp'])->makePartial()->shouldAllowMockingProtectedMethods();
//        $applicationDetector->shouldReceive('initBootstrapApp')->once();
        self::assertInstanceOf(Container::class, $applicationDetector->getContainer());
    }
}
