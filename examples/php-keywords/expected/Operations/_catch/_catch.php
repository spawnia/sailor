<?php

declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_catch;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\PhpKeywords\Operations\_catch\_Print\_Switch|\Spawnia\Sailor\PhpKeywords\Operations\_catch\_Print\AnotherType|null $print
 */
class _catch extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\PhpKeywords\Operations\_catch\_Print\_Switch|\Spawnia\Sailor\PhpKeywords\Operations\_catch\_Print\AnotherType|null $print
     */
    public static function make($print = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($print !== self::UNDEFINED) {
            $instance->print = $print;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'print' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'Switch' => '\\Spawnia\\Sailor\\PhpKeywords\\Operations\\_catch\\_Print\\_Switch',
            'AnotherType' => '\\Spawnia\\Sailor\\PhpKeywords\\Operations\\_catch\\_Print\\AnotherType',
        ])),
        ];
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
