<?php

namespace ByTIC\Console\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTest
 * @package ByTIC\Console\Tests
 */
abstract class AbstractTest extends TestCase
{
    protected $object;

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
}
