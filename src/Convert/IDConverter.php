<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

/** @see https://spec.graphql.org/draft/#sec-ID */
class IDConverter implements TypeConverter
{
    /**
     * Since the GraphQL spec only recommends ID to be serialized to string, we are
     * liberal in what we accept.
     */
    public function fromGraphQL($value): string
    {
        return $this->toString($value);
    }

    public function toGraphQL($value): string
    {
        return $this->toString($value);
    }

    /** @param mixed $value Should be int or string */
    protected function toString($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (string) $value;
        }

        $notIntOrString = gettype($value);
        throw new \InvalidArgumentException("Expected int|string, got: {$notIntOrString}");
    }
}
