<?php declare(strict_types=1);

use Spawnia\Sailor\Polymorphic\Operations\UserOrPost;

require __DIR__ . '/../vendor/autoload.php';

$id = '1';

$result = UserOrPost::execute($id);
$userOrPost = $result->data->node;

assert($userOrPost instanceof UserOrPost\Node\User);
assert($userOrPost->id === $id);
assert($userOrPost->name === 'blarg');
