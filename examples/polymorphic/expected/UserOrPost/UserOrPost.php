<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost;

class UserOrPost extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPost\User|\Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPost\Post */
    public $userOrPost;

    public function userOrPostTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\PolymorphicMapper([
            'User' => \Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPost\User::class,
            'Post' => \Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPost\Post::class,
        ]);
    }
}
