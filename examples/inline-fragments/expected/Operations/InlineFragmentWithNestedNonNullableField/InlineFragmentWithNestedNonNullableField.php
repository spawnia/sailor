<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField;

/**
 * @property string $__typename
 * @property array<int, \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Article|\Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Video>|null $search
 */
class InlineFragmentWithNestedNonNullableField extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int, \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Article|\Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Video>|null $search
     */
    public static function make(
        $search = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($search !== self::UNDEFINED) {
            $instance->__set('search', $search);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'search' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'Article' => '\\Spawnia\\Sailor\\InlineFragments\\Operations\\InlineFragmentWithNestedNonNullableField\\Search\\Article',
            'Video' => '\\Spawnia\\Sailor\\InlineFragments\\Operations\\InlineFragmentWithNestedNonNullableField\\Search\\Video',
        ])))),
        ];
    }

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
