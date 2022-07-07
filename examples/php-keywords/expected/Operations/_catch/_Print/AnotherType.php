<?php

declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_catch\_Print;

/**
 * @property string $__typename
 * @property int|null $a
 */
class AnotherType extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $a
     */
    public static function make($a = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'AnotherType';
        if ($a !== self::UNDEFINED) {
            $instance->a = $a;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'a' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../../sailor.php';
    }
}
