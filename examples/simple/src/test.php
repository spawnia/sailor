<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

$result = \Spawnia\Sailor\Simple\MyScalarQuery::execute();

echo $result->data->scalarWithArg;
