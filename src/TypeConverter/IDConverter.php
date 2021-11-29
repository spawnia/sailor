<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

/**
 * https://spec.graphql.org/draft/#sec-ID.
 */
class IDConverter implements TypeConverter
{
    /**
     * Since the GraphQL spec only recommends ID to be serialized to string, we are
     * liberal in what we accept.
     */
    public function fromGraphQL($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (string) $value;
        }

        throw new \InvalidArgumentException('Expected int|string, got: '.gettype($value));
    }

    public function toGraphQL($value): string
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got '.gettype($value));
        }

        return $value;
    }
}
