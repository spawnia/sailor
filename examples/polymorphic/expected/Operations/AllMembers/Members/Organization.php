<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members;

/**
 * @property string $code
 * @property string $__typename
 */
class Organization extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $code
     */
    public static function make($code): self
    {
        $instance = new self;

        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }
        $instance->__typename = 'Organization';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'code' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../../sailor.php';
    }
}
