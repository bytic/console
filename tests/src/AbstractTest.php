<?php

declare(strict_types=1);

namespace ByTIC\Console\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends TestCase
{
    protected $object;

    public function tearDown(): void
    {
        \ByTIC\Console\Application::clearBootstrappers();
        parent::tearDown();
        m::close();
    }
}
