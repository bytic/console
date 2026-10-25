<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

require dirname(__DIR__) . '/vendor/autoload.php';

$directory = sys_get_temp_dir() . '/bytic console consumer ' . bin2hex(random_bytes(8));
mkdir($directory, 0777, true);

$run = static function (array $command, ?string $cwd = null) use ($directory): Process {
    $process = new Process($command, $cwd ?? $directory, ['XDEBUG_MODE' => 'off']);
    $process->setTimeout(300);
    $process->mustRun();

    return $process;
};

try {
    $manifest = [
        'name' => 'bytic/console-consumer-test',
        'repositories' => [[
            'type' => 'path',
            'url' => dirname(__DIR__),
            'options' => ['symlink' => false, 'versions' => ['bytic/console' => '2.0.0']],
        ]],
        'require' => [
            'bytic/console' => '^2.0',
            'bytic/application' => '^1.0 || ^2.0',
            'bytic/container' => '^1.0 || ^2.0',
            'bytic/dispatcher' => '^1.0 || ^2.0',
            'bytic/router' => '^1.0 || ^2.0',
            'bytic/staging' => '^1.0 || ^2.0',
            'symfony/console' => getenv('CONSOLE_VERSION') ?: '^8.0',
            'symfony/process' => getenv('PROCESS_VERSION') ?: '^8.0',
            'symfony/finder' => getenv('FINDER_VERSION') ?: '^8.0',
        ],
        'autoload' => [
            'psr-4' => ['App\\' => 'application/', 'App\\Library\\' => 'Library/'],
        ],
        'config' => ['allow-plugins' => new stdClass()],
    ];
    file_put_contents($directory . '/composer.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

    $run(['composer', 'update', '--no-dev', '--no-interaction', '--no-plugins', '--no-scripts', '--prefer-dist', '--no-progress']);
    $binary = $directory . '/vendor/bin/bytic';
    $list = $run([PHP_BINARY, $binary, 'list', '--raw'], sys_get_temp_dir());
    if (!str_contains($list->getOutput(), 'list')) {
        throw new RuntimeException('Installed console did not list its built-in commands.');
    }

    foreach ([
        'bootstrap/app.php',
        'Library/Application.php',
        'config/app.php',
        'application/Console/Model/TestCommand.php',
    ] as $file) {
        $target = $directory . '/' . $file;
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }
        copy(__DIR__ . '/fixtures/demoapp/' . $file, $target);
    }
    file_put_contents($directory . '/.env', '');
    $run(['composer', 'dump-autoload', '--no-dev', '--no-interaction', '--no-plugins', '--no-scripts']);
    $command = $run([PHP_BINARY, $binary, 'app:test'], sys_get_temp_dir());
    if (!str_contains($command->getOutput(), 'Fixture command')) {
        throw new RuntimeException('Installed console did not discover and run the application command.');
    }

    file_put_contents($directory . '/bootstrap/app.php', '<?php return null;');
    $failure = new Process([PHP_BINARY, $binary, 'list'], sys_get_temp_dir(), ['XDEBUG_MODE' => 'off']);
    $failure->run();
    if ($failure->getExitCode() !== 1
        || !str_contains($failure->getErrorOutput(), 'must return a ByTIC application')) {
        throw new RuntimeException('Invalid application bootstrap must fail with a diagnostic and exit status 1.');
    }
    echo "Production install, command discovery, and startup failure checks passed.\n";
} finally {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );
    foreach ($files as $file) {
        if ($file->isDir() && !$file->isLink()) {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($directory);
}
