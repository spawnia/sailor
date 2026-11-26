<?php declare(strict_types=1);

namespace Spawnia\Sailor;

/** Base for generated variants of a GraphQL OneOf input object. */
abstract class OneOfInput extends ObjectLike
{
    protected function __construct() {}

    public function __set(string $name, $value): void
    {
        if ($value === null) {
            throw new \InvalidArgumentException("OneOf input field {$name} must not be null.");
        }

        parent::__set($name, $value);
    }
}
