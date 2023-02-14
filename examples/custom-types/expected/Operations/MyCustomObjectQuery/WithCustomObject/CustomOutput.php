<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomObjectQuery\WithCustomObject;

/**
 * @property string $foo
 * @property string $__typename
 */
class CustomOutput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $foo
     */
    public static function make($foo): self
    {
        $instance = new self;

        if ($foo !== self::UNDEFINED) {
            $instance->foo = $foo;
        }
        $instance->__typename = 'CustomOutput';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'foo' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../sailor.php');
    }
}
