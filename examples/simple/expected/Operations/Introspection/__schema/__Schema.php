<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\Introspection\__schema;

/**
 * @property \Spawnia\Sailor\Simple\Operations\Introspection\__schema\QueryType\__Type $queryType
 * @property string $__typename
 */
class __Schema extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Simple\Operations\Introspection\__schema\QueryType\__Type $queryType
     */
    public static function make($queryType): self
    {
        $instance = new self;

        if ($queryType !== self::UNDEFINED) {
            $instance->__set('queryType', $queryType);
        }
        $instance->__typename = '__Schema';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'queryType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Simple\Operations\Introspection\__schema\QueryType\__Type),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
