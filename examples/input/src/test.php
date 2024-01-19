<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$result = Spawnia\Sailor\Input\Operations\TakeSomeInput::execute();

assert($result->data->takeSomeInput === 42);
