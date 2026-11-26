<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Types;

/**
 * @property array<\Spawnia\Sailor\Input\Types\Lookup\ById|\Spawnia\Sailor\Input\Types\Lookup\ByIds> $lookups
 */
class LookupBatch extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\Spawnia\Sailor\Input\Types\Lookup\ById|\Spawnia\Sailor\Input\Types\Lookup\ByIds> $lookups
     */
    public static function make($lookups): self
    {
        $instance = new self;

        if ($lookups !== self::UNDEFINED) {
            $instance->__set('lookups', $lookups);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'lookups' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Input\Types\Lookup\Converter))),
        ];
    }

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
