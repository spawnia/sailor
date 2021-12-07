<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TwoArgs;

/**
 * @property string $__typename
 * @property string|null $twoArgs
 */
class TwoArgs extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string|null $twoArgs
     */
    public static function make(?string $twoArgs = null): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        $instance->twoArgs = $twoArgs;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'twoArgs' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
        ];
    }
}
