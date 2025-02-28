<?php declare(strict_types=1);

use Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;
use Spawnia\Sailor\CustomTypes\Operations\MyCustomObjectQuery;
use Spawnia\Sailor\CustomTypes\Types\CustomEnum;
use Spawnia\Sailor\CustomTypesSrc\CustomObject;

require __DIR__ . '/../vendor/autoload.php';

$myCustomEnumQueryResult = MyCustomEnumQuery::execute(
    new CustomEnum(CustomEnum::A)
);
assert($myCustomEnumQueryResult->data->withCustomEnum->value === CustomEnum::B);

$foo = 'foo';
$myCustomObjectQueryResult = MyCustomObjectQuery::execute(
    new CustomObject($foo)
);
assert($myCustomObjectQueryResult->data->withCustomObject->foo === $foo);
