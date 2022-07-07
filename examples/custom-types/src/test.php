<?php declare(strict_types=1);

use Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;
use Spawnia\Sailor\CustomTypes\Types\CustomEnum;

require __DIR__ . '/../vendor/autoload.php';

$result = MyCustomEnumQuery::execute(
    new CustomEnum(CustomEnum::A)
);

assert(CustomEnum::B === $result->data->withCustomEnum->value);
