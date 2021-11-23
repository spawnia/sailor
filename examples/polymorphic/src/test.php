<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

$result = \Spawnia\Sailor\Polymorphic\UserOrPost::execute();
$userOrPost = $result->data->userOrPost;

assert($userOrPost instanceof \Spawnia\Sailor\Simple\UserOrPost\UserOrPost\User);
assert($userOrPost->id === '1');
assert($userOrPost->name === 'blarg');
