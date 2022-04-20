<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members;

/**
 * @property string $__typename
 */
class Organization extends \Spawnia\Sailor\ObjectLike
{
    public static function make(): self
    {
        $instance = new self;

        $instance->__typename = 'Member';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
