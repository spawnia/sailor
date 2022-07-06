<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Types;

/**
 * @property string|null $unset
 */
class _new extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $unset
     */
    public static function make($unset = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        if ($unset !== self::UNDEFINED) {
            $instance->unset = $unset;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'unset' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../sailor.php';
    }
}
