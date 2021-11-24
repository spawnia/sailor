<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost;

class UserOrPost extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Polymorphic\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\UserOrPost\Node\Task */
    public $node;

    public function nodeTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\PolymorphicMapper([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\UserOrPost\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\UserOrPost\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\UserOrPost\\Node\\Task',
        ]);
    }
}
