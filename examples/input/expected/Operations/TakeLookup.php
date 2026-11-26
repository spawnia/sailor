<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Input\Operations\TakeLookup\TakeLookupResult>
 */
class TakeLookup extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\Input\Types\LookupBatch $batch
     * @param \Spawnia\Sailor\Input\Types\Lookup\ById|\Spawnia\Sailor\Input\Types\Lookup\ByIds|null $lookup
     */
    public static function execute(
        $batch,
        $lookup = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): TakeLookup\TakeLookupResult {
        return self::executeOperation(
            $batch,
            $lookup,
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
            ['batch', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Input\Types\LookupBatch)],
            ['lookup', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Input\Types\Lookup\Converter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'mutation TakeLookup($batch: LookupBatch!, $lookup: Lookup) {
          __typename
          takeLookup(batch: $batch, lookup: $lookup)
        }';
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
