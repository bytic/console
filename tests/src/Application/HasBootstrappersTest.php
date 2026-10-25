<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests\Application;

use ByTIC\Console\Application;
use ByTIC\Console\Tests\AbstractTest;

/**
 * Class HasBootstrappersTest
 */
class HasBootstrappersTest extends AbstractTest
{
    public function testStarting()
    {
        Application::starting(function ($application) {
            $application->setName('TEST');
        });

        $application = new Application();

        self::assertSame('TEST', $application->getName());
    }
}
