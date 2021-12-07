<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node;

/**
 * @property string $id
 * @property string $__typename
 * @property string|null $title
 */
class Post extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string $id
     * @param string|null $title
     */
    public static function make(string $id, ?string $title = null): self
    {
        $instance = new self;

        $instance->id = $id;
        $instance->__typename = 'Node';
        $instance->title = $title;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}
