<?php

declare(strict_types=1);

include __DIR__.'/vendor/autoload.php';

$result = \Spawnia\Sailor\Foo\Foo::execute();

echo $result->data->foo;
