<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Mockery;
use Mockery\MockInterface;

/**
 * Subclasses of this class are automatically generated.
 *
 * They must implement a public function called `execute`.
 * `execute` can not be made into an actual abstract function, since
 * its arguments and return type should be strictly typed
 * depending on the contents of the operation.
 *
 * @template TResult of Result
 */
abstract class Operation
{
    /**
     * Map from child classes to their registered mocks.
     *
     * @var array<class-string<static>, static&MockInterface>
     */
    protected static array $mocks = [];

    /**
     * A client to use over the client from the endpoint config.
     *
     * @var array<class-string<static>, Client|null>
     */
    protected static array $clients = [];

    /**
     * The configured endpoint the operation belongs to.
     */
    abstract public static function endpoint(): string;

    /**
     * The GraphQL query string.
     */
    abstract public static function document(): string;

    /**
     * @param  mixed  ...$args
     * @return TResult
     */
    protected static function executeOperation(...$args): Result
    {
        $mock = self::$mocks[static::class] ?? null;
        if ($mock !== null) {
            // @phpstan-ignore-next-line This function is only present on child classes
            return $mock::execute(...$args);
        }

        $response = self::fetchResponse($args);

        $child = static::class;
        $parts = explode('\\', $child);
        $basename = end($parts);

        /** @var class-string<TResult> $resultClass */
        $resultClass = $child.'\\'.$basename.'Result';

        return $resultClass::fromResponse($response);
    }

    /**
     * Send an operation through the client and return the response.
     *
     * @param  array<int, mixed>  $args
     */
    protected static function fetchResponse(array $args): Response
    {
        $variables = new \stdClass();
        $executeMethod = new \ReflectionMethod(static::class, 'execute');
        $parameters = $executeMethod->getParameters();
        foreach ($args as $index => $arg) {
            $parameter = $parameters[$index];
            $variables->{$parameter->getName()} = $arg;
        }

        $client = self::$clients[static::class]
            ?? Configuration::endpoint(static::endpoint())
                ->makeClient();

        return $client->request(static::document(), $variables);
    }

    /**
     * @return static&MockInterface
     */
    public static function mock(): MockInterface
    {
        // @phpstan-ignore-next-line The type of MockInterface matches up, I promise
        return self::$mocks[static::class]
            ??= Mockery::mock(static::class);
    }

    public static function clearMocks(): void
    {
        self::$mocks = [];
    }

    public static function setClient(?Client $client): void
    {
        self::$clients[static::class] = $client;
    }
}
