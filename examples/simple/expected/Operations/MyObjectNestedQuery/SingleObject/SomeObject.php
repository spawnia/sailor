<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null $nested
 */
class SomeObject extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null $nested
     */
    public static function make(
        $nested = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self
    {
        $instance = new self;

        $instance->__typename = 'SomeObject';
        if ($nested !== self::UNDEFINED) {
            $instance->nested = $nested;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'nested' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../sailor.php');
    }
}
