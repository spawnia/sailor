<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeSomeInput;

/**
 * @property string $__typename
 * @property int|null $takeSomeInput
 */
class TakeSomeInput extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param int|null $takeSomeInput
     */
    public static function make(?int $takeSomeInput = null): self
    {
        $instance = new self;

        $instance->__typename = 'Mutation';
        $instance->takeSomeInput = $takeSomeInput;

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
