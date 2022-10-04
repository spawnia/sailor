<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node;

/**
 * @property string $id
 * @property string $__typename
 */
class Task extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     */
    public static function make($id): self
    {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        $instance->__typename = 'Task';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../sailor.php');
    }
}
