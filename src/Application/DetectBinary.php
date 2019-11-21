<?php

namespace ByTIC\Console\Application;

use ByTIC\Console\Support\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Trait DetectBinary
 * @package ByTIC\Console\Application
 */
trait DetectBinary
{

    /**
     * Determine the proper PHP executable.
     *
     * @return string
     */
    public static function phpBinary()
    {
        return ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
    }

    /**
     * Determine the proper Bytic executable.
     *
     * @return string
     */
    public static function byticBinary()
    {
        return defined('BYTIC_BINARY') ? ProcessUtils::escapeArgument(BYTIC_BINARY) : 'bytic';
    }
}