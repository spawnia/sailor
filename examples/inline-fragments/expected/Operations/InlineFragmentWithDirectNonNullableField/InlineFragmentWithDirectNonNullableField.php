<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField;

/**
 * @property array<int, \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField\Search\Article|\Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField\Search\Video> $search
 * @property string $__typename
 */
class InlineFragmentWithDirectNonNullableField extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int, \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField\Search\Article|\Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField\Search\Video> $search
     */
    public static function make($search): self
    {
        $instance = new self;

        if ($search !== self::UNDEFINED) {
            $instance->__set('search', $search);
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'search' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'Article' => '\\Spawnia\\Sailor\\InlineFragments\\Operations\\InlineFragmentWithDirectNonNullableField\\Search\\Article',
            'Video' => '\\Spawnia\\Sailor\\InlineFragments\\Operations\\InlineFragmentWithDirectNonNullableField\\Search\\Video',
        ])))),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
