<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipGrandparentThenSelect\SingleObject\Nested;

/**
 * @property string $__typename
 * @property int|null $value
 * @property \Spawnia\Sailor\Simple\Operations\SkipGrandparentThenSelect\SingleObject\Nested\Nested\SomeObject|null $nested
 */
class SomeObject extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $value
     * @param \Spawnia\Sailor\Simple\Operations\SkipGrandparentThenSelect\SingleObject\Nested\Nested\SomeObject|null $nested
     */
    public static function make(
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $nested = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'SomeObject';
        if ($value !== self::UNDEFINED) {
            $instance->__set('value', $value);
        }
        if ($nested !== self::UNDEFINED) {
            $instance->__set('nested', $nested);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\OmittableConverter(new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter)),
            'nested' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\SkipGrandparentThenSelect\SingleObject\Nested\Nested\SomeObject),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../sailor.php');
    }
}
