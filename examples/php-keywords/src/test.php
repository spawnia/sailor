<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Spawnia\Sailor\PhpKeywords\Operations\_catch;
use Spawnia\Sailor\PhpKeywords\Operations\_catch\_Print\_Switch;
use Spawnia\Sailor\PhpKeywords\Types\_abstract;

$result = _catch::execute();

$switch = $result->data->print;
assert($switch instanceof _Switch);

assert(_abstract::_class === $switch->for);
assert(42 === $switch->int);
assert(69 === $switch->as);
