<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Spawnia\Sailor\PhpKeywords\Operations\_Catch;
use Spawnia\Sailor\PhpKeywords\Operations\AllCases;
use Spawnia\Sailor\PhpKeywords\Types\_abstract;

$catchResult = _Catch::execute();

$switch = $catchResult->data->print;
assert($switch instanceof _Catch\_Print\_Switch);
assert($switch->for === _abstract::_class);
assert($switch->int === 42);
assert($switch->as === 69);

$allCasesResult = AllCases::execute();

$cases = $allCasesResult->data->cases;
assert(is_array($cases));

$case1 = $cases[0];
assert($case1 instanceof AllCases\Cases\_Case);
assert($case1->id === 'asdf');
