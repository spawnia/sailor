<?php

declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

$id = '1';

$result = \Spawnia\Sailor\Polymorphic\UserOrPost::execute($id);
$userOrPost = $result->data->node;

assert($userOrPost instanceof \Spawnia\Sailor\Polymorphic\UserOrPost\Node\User);
assert($userOrPost->id === $id);
assert('blarg' === $userOrPost->name);
