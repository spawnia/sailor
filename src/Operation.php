<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Mockery;
use Mockery\MockInterface;
use Spawnia\Sailor\Convert\TypeConverter;
use Spawnia\Sailor\Event\EndRequest;
use Spawnia\Sailor\Event\StartRequest;

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
abstract class Operation implements BelongsToEndpoint
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
     * The GraphQL query string.
     */
    abstract public static function document(): string;

    /**
     * @return array<int, array{string, TypeConverter}>
     */
    abstract protected static function converters(): array;

    /**
     * @param  mixed  ...$args type depends on the subclass
     *
     * @return TResult
     */
    protected static function executeOperation(...$args): Result
    {
        $mock = self::$mocks[static::class] ?? null;
        if (null !== $mock) {
            // @phpstan-ignore-next-line This function is only present on child classes
            return $mock::execute(...$args);
        }

        $response = self::fetchResponse($args);

        $child = static::class;
        $parts = explode('\\', $child);
        $basename = end($parts);

        /** @var class-string<TResult> $resultClass */
        $resultClass = $child . '\\' . $basename . 'Result';

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
        $arguments = static::converters();
        foreach ($args as $index => $arg) {
            if (ObjectLike::UNDEFINED === $arg) {
                continue;
            }

            [$name, $typeConverter] = $arguments[$index];
            $variables->{$name} = $typeConverter->toGraphQL($arg);
        }

        $endpointConfig = Configuration::endpoint(static::config(), static::endpoint());

        $client = self::$clients[static::class]
            ?? $endpointConfig->makeClient();

        $document = static::document();

        $endpointConfig->fireEvent(new StartRequest($document, $variables));
        $response = $client->request($document, $variables);
        $endpointConfig->fireEvent(new EndRequest($response));

        return $response;
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
