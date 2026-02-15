<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$result = Spawnia\Sailor\Simple\Operations\MyObjectQuery::execute();

assert($result->data->singleObject->value === 42);
