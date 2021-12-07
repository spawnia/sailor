<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost;

class UserOrPost extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task */
    public $node;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter));
    }

    public function nodeTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\Task',
        ])));
    }
}
