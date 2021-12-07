<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyScalarQuery;

/**
 * @property string $__typename
 * @property string|null $scalarWithArg
 */
class MyScalarQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string|null $scalarWithArg
     */
    public static function make(?string $scalarWithArg = null): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        $instance->scalarWithArg = $scalarWithArg;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'scalarWithArg' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
        ];
    }
}
