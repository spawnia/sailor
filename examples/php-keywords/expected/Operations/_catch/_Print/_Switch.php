<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\_catch\_Print;

/**
 * @property string $__typename
 * @property string|null $for
 */
class _Switch extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $for
     */
    public static function make($for = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Switch';
        if ($for !== self::UNDEFINED) {
            $instance->for = $for;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'for' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
