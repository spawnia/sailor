<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use stdClass;

use function Safe\json_decode;
use function Safe\json_encode;

/**
 * @phpstan-type JsonValue array<int, mixed>|array<string, mixed>|stdClass|string|float|int|bool|null
 * @phpstan-type StdClassJsonValue array<int, mixed>|stdClass|string|float|int|bool|null
 * @phpstan-type AssocJsonValue array<int, mixed>|stdClass|string|float|int|bool|null
 */
final class Json
{
    /**
     * Convert a JSON-encodable value so that maps are stdClass instances.
     *
     * @param JsonValue $value any value that can be encoded as JSON
     *
     * @return StdClassJsonValue the transformed value where associative arrays are now stcClass instances
     */
    public static function assocToStdClass($value)
    {
        return json_decode(json_encode($value)); // @phpstan-ignore return.type (functions from safe-php return mixed)
    }

    /**
     * Convert a JSON-encodable value so that maps are associative arrays.
     *
     * @param JsonValue $value any value that can be encoded as JSON
     *
     * @return AssocJsonValue the transformed value where objects are now associative arrays
     */
    public static function stdClassToAssoc($value)
    {
        return json_decode(json_encode($value), true); // @phpstan-ignore return.type (functions from safe-php return mixed)
    }
}
