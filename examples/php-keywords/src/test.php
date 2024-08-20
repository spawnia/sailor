<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Spawnia\Sailor\PhpKeywords\Operations\_Catch;
use Spawnia\Sailor\PhpKeywords\Operations\_Catch\_Print\_Switch;
use Spawnia\Sailor\PhpKeywords\Types\_abstract;

$result = _Catch::execute();

$switch = $result->data->print;
assert($switch instanceof _Switch);

assert($switch->for === _abstract::_class);
assert($switch->int === 42);
assert($switch->as === 69);
