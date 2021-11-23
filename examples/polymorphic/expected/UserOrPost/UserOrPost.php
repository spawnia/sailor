<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost;

class UserOrPost extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Polymorphic\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\UserOrPost\Node\Task|\Spawnia\Sailor\Polymorphic\UserOrPost\Node\User */
    public $userOrPost;

    public function userOrPostTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\PolymorphicMapper([
            'Post' => \Spawnia\Sailor\Polymorphic\UserOrPost\Node\Post::class,
            'Task' => \Spawnia\Sailor\Polymorphic\UserOrPost\Node\Task::class,
            'User' => \Spawnia\Sailor\Polymorphic\UserOrPost\Node\User::class,
        ]);
    }
}
