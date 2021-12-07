<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$result = \Spawnia\Sailor\Simple\Operations\MyObjectQuery::execute();

assert(42 === $result->data->singleObject->value);
