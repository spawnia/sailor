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
 */
abstract class Operation
{
    /**
     * Map from child classes to their registered mocks.
     *
     * @var array<class-string<static>, static&MockInterface>
     */
    private static array $mocks = [];

    /**
     * The configured endpoint the operation belongs to.
     */
    abstract public static function endpoint(): string;

    /**
     * The GraphQL query string.
     */
    abstract public static function document(): string;

    /**
     * @param mixed ...$args
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

        /** @var class-string<\Spawnia\Sailor\Result> $resultClass */
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

        return Configuration
            ::endpoint(static::endpoint())
            ->makeClient()
            ->request(static::document(), $variables);
    }

    /**
     * @return static&MockInterface
     */
    public static function mock(): MockInterface
    {
        /** @var static&MockInterface $mock */
        $mock = Mockery::mock(static::class);
        self::$mocks[static::class] = $mock;

        return $mock;
    }

    public static function clearMocks(): void
    {
        self::$mocks = [];
    }
}
