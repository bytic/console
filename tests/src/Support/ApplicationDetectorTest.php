<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Support;

use ByTIC\Console\Support\ApplicationDetector;
use ByTIC\Console\Tests\AbstractTest;
use Nip\Container\Container;

/**
 * Class ApplicationDetectorTest
 */
class ApplicationDetectorTest extends AbstractTest
{
    public function test_getContainer()
    {
        $probe = static function (\Throwable $exception): void {};
        $previous = set_exception_handler($probe);
        restore_exception_handler();
        try {
            $applicationDetector = new ApplicationDetector(TEST_FIXTURE_PATH . '/demoapp');
            $container = $applicationDetector->getContainer();
            self::assertInstanceOf(Container::class, $container);
            self::assertSame($container, $applicationDetector->getContainer());
            self::assertSame(TEST_FIXTURE_PATH . '/demoapp/application', (string) $container->get('path'));
        } finally {
            // ByTIC's bootstrap can install a process-wide exception handler.
            $installed = set_exception_handler($probe);
            restore_exception_handler();
            if ($installed !== $previous) {
                restore_exception_handler();
            }
        }
    }

    public function testInvalidBaseDirectoryFails(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ApplicationDetector(TEST_FIXTURE_PATH . '/missing');
    }

    public function testMissingApplicationIsOptional(): void
    {
        self::assertNull((new ApplicationDetector(PROJECT_BASE_PATH))->getContainer());
    }
}
