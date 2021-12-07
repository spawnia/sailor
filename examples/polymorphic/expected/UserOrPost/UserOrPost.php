<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost;

class UserOrPost extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Polymorphic\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\UserOrPost\Node\Task */
    public $node;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function nodeTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\UserOrPost\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\UserOrPost\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\UserOrPost\\Node\\Task',
        ])));
    }
}
