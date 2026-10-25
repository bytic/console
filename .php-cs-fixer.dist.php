<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'no_empty_phpdoc' => true,
        'no_extra_blank_lines' => true,
        'phpdoc_no_package' => true,
    ])
    ->setFinder(
        Finder::create()
            ->in([__DIR__ . '/src', __DIR__ . '/bootstrap', __DIR__ . '/tests'])
            ->append([__DIR__ . '/bin/bytic', __FILE__]),
    );
