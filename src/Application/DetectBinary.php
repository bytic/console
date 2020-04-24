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
        static $path;
        if (empty($path)) {
            $path = static::byticBinaryFinder();
        }
        return $path;
    }

    /**
     * @return string
     */
    protected static function byticBinaryFinder()
    {
        if (defined('BYTIC_BINARY')) {
            return ProcessUtils::escapeArgument(BYTIC_BINARY);
        }
        require dirname(dirname(__DIR__)) . '/bootstrap/constants.php';
        return BYTIC_CONSOLE_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'bytic';
    }
}