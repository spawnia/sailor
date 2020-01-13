<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * Subclasses of this class are automatically generated.
 *
 * They must implement a public function called `execute`.
 * `execute` can not be made into an actual abstract function, since
 * its arguments and return type should be strictly typed
 * depending on the contents of the operation.
 */
abstract class Operation
{
    /**
     * The configured endpoint the operation belongs to.
     */
    abstract public static function endpoint(): string;

    /**
     * The GraphQL query string.
     */
    abstract public static function document(): string;

    /**
     * Send an operation through the client and return the response.
     *
     * @param  mixed  ...$args
     */
    protected static function fetchResponse(...$args): Response
    {
        $variables = new \stdClass();
        $executeMethod = new \ReflectionMethod(static::class, 'execute');
        $parameters = $executeMethod->getParameters();
        foreach ($args as $index => $arg) {
            $parameter = $parameters[$index];
            $variables->{$parameter->getName()} = $arg;
        }

        return Configuration
            ::forEndpoint(static::endpoint())
            ->client()
            ->request(static::document(), $variables);
    }
}
