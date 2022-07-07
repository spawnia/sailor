#!/usr/bin/env php
<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Spawnia\Sailor\Tests\Examples;

foreach (Examples::EXAMPLES as $example) {
    $examplePath = Examples::examplePath($example);

    shell_exec("cd {$examplePath}");
    shell_exec("./test.sh");
}
