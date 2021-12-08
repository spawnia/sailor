<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TwoArgs;

/**
 * @property string $__typename
 * @property string|null $twoArgs
 */
class TwoArgs extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string|null $twoArgs
     */
    public static function make($twoArgs = 1.7976931348623157E+308): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($twoArgs !== self::UNDEFINED) {
            $instance->twoArgs = $twoArgs;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'twoArgs' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
        ];
    }
}
