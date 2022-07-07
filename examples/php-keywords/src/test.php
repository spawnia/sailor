<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$result = \Spawnia\Sailor\PhpKeywords\Operations\_catch::execute();

assert(\Spawnia\Sailor\PhpKeywords\Types\_abstract::_class === $result->data->print->for);
