<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Types\Lookup;

/**
 * @property array<int|string> $ids
 */
final class ByIds extends \Spawnia\Sailor\OneOfInput
{
    /**
     * @param array<int|string> $value
     */
    public static function make($value): self
    {
        $instance = new self;

        $instance->__set('ids', $value);

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'ids' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
        ];
    }

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
