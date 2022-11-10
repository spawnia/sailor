<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipNonNullable;

/**
 * @property string $nonNullable
 * @property string $__typename
 */
class SkipNonNullable extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $nonNullable
     */
    public static function make($nonNullable): self
    {
        $instance = new self;

        if ($nonNullable !== self::UNDEFINED) {
            $instance->nonNullable = $nonNullable;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'nonNullable' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
