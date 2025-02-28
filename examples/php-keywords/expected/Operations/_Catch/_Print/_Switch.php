<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_Catch\_Print;

/**
 * @property string $__typename
 * @property int|null $int
 * @property string|null $for
 * @property int|null $as
 */
class _Switch extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $int
     * @param string|null $for
     * @param int|null $as
     */
    public static function make(
        $int = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $for = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $as = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Switch';
        if ($int !== self::UNDEFINED) {
            $instance->int = $int;
        }
        if ($for !== self::UNDEFINED) {
            $instance->for = $for;
        }
        if ($as !== self::UNDEFINED) {
            $instance->as = $as;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'int' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'for' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'as' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../sailor.php');
    }
}
