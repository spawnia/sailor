<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ClientDirectiveQuery;

/**
 * @property string $__typename
 * @property string|null $scalarWithArg
 * @property string|null $twoArgs
 */
class ClientDirectiveQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $scalarWithArg
     * @param string|null $twoArgs
     */
    public static function make(
        $scalarWithArg = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $twoArgs = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'
    ): self {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($scalarWithArg !== self::UNDEFINED) {
            $instance->scalarWithArg = $scalarWithArg;
        }
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
            'scalarWithArg' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'twoArgs' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
