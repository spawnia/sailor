<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyScalarQuery;

/**
 * @property string $__typename
 * @property string|null $scalarWithArg
 */
class MyScalarQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $scalarWithArg
     */
    public static function make($scalarWithArg = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($scalarWithArg !== self::UNDEFINED) {
            $instance->scalarWithArg = $scalarWithArg;
        }

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

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/simple/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
