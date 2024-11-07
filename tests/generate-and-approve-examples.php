#!/usr/bin/env php
<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Spawnia\Sailor\Tests\Examples;

foreach (Examples::EXAMPLES as $example) {
    Examples::generate($example);

    $expectedPath = Examples::expectedPath($example);
    shell_exec("rm -rf {$expectedPath}");

    $generatedPath = Examples::generatedPath($example);
    # Using -R over --recursive for macOS compatibility
    shell_exec("cp -R {$generatedPath} {$expectedPath}");
}
