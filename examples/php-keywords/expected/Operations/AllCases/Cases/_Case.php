<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\AllCases\Cases;

/**
 * @property string $id
 * @property string $__typename
 */
class _Case extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     */
    public static function make($id): self
    {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->__set('id', $id);
        }
        $instance->__typename = 'Case';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
