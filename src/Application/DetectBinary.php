<?php

declare(strict_types=1);

namespace ByTIC\Console\Application;

use Symfony\Component\Process\PhpExecutableFinder;

trait DetectBinary
{
    public static function phpBinary(): string
    {
        $binary = (new PhpExecutableFinder())->find(false);
        if ($binary === false) {
            throw new \RuntimeException('Unable to locate a PHP executable.');
        }

        return $binary;
    }

    public static function byticBinary(): string
    {
        if (defined('BYTIC_BINARY')) {
            return BYTIC_BINARY;
        }
        if (defined('BYTIC_CONSOLE_ROOT_DIR')) {
            $proxy = BYTIC_CONSOLE_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'bytic';
            if (is_file($proxy)) {
                return $proxy;
            }
        }

        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'bytic';
    }
}
