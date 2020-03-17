<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

$result = \Spawnia\Sailor\Simple\MyObjectQuery::execute();

assert($result->data->singleObject->value === 42);
