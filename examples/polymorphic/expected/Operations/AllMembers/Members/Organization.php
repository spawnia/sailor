<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members;

/**
 * @property string $code
 * @property string $__typename
 */
class Organization extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string $code
     */
    public static function make(string $code): self
    {
        $instance = new self;

        $instance->code = $code;
        $instance->__typename = 'Member';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'code' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}
