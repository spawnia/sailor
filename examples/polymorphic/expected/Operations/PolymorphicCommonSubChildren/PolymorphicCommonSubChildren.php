<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Sub $sub
 * @property string $__typename
 */
class PolymorphicCommonSubChildren extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Sub $sub
     */
    public static function make($sub): self
    {
        $instance = new self;

        if ($sub !== self::UNDEFINED) {
            $instance->sub = $sub;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'sub' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Sub),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
