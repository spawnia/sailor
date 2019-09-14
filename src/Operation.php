<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * Subclasses of this class are automatically generated.
 *
 * They must implement the following abstract function:
 * public abstract function execute(mixed[] ...$args): mixed
 */
abstract class Operation
{
    /**
     * The configured endpoint the operation belongs to.
     *
     * @return string
     */
    abstract public static function endpoint(): string;

    /**
     * The GraphQL query string.
     *
     * @return string
     */
    abstract public static function document(): string;

    // TODO pass variables
    protected static function fetchResponse(): Response
    {
        return Configuration
            ::forEndpoint(static::endpoint())
            ->client()
            ->request(static::document());
    }
}
