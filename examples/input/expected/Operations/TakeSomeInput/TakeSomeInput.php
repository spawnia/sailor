<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeSomeInput;

/**
 * @property string $__typename
 * @property int|null $takeSomeInput
 */
class TakeSomeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $takeSomeInput
     */
    public static function make($takeSomeInput = 1.7976931348623157E+308): self
    {
        $instance = new self;

        $instance->__typename = 'Mutation';
        if ($takeSomeInput !== self::UNDEFINED) {
            $instance->takeSomeInput = $takeSomeInput;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'takeSomeInput' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }
}
