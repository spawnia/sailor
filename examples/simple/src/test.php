<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

$result = \Spawnia\Sailor\Simple\MyObjectQuery::execute();

echo $result->data->singleObject->value;
