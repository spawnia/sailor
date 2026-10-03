<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyStringLiteralsQuery;

/**
 * @property string $__typename
 * @property string|null $singleQuote
 * @property string|null $backslash
 */
class MyStringLiteralsQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $singleQuote
     * @param string|null $backslash
     */
    public static function make(
        $singleQuote = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $backslash = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($singleQuote !== self::UNDEFINED) {
            $instance->__set('singleQuote', $singleQuote);
        }
        if ($backslash !== self::UNDEFINED) {
            $instance->__set('backslash', $backslash);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'singleQuote' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'backslash' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
