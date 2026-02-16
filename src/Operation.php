<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Mockery\MockInterface;
use Spawnia\Sailor\Convert\TypeConverter;
use Spawnia\Sailor\Events\ReceiveResponse;
use Spawnia\Sailor\Events\StartRequest;

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

    /** If true, no operations can execute without being mocked. */
    protected static bool $requireMocks = false;

    /**
     * A client to use over the client from the endpoint config.
     *
     * @var array<class-string<static>, Client|null>
     */
    protected static array $clients = [];

    /** The GraphQL query string. */
    abstract public static function document(): string;

    /** @return array<int, array{string, TypeConverter}> */
    abstract protected static function converters(): array;

    /**
     * @param  mixed  ...$args type depends on the subclass
     *
     * @return TResult
     */
    protected static function executeOperation(...$args): Result
    {
        $childClass = static::class;

        $mock = self::$mocks[$childClass] ?? null;
        if ($mock !== null) {
            // @phpstan-ignore staticMethod.notFound,return.type (only present on child classes)
            return $mock::execute(...$args);
        }

        if (self::$requireMocks) {
            $endpoint = static::endpoint();
            throw new \Exception("Tried to execute a Sailor operation on endpoint {$endpoint}, but no mock for was registered for {$childClass}.");
        }

        $response = static::fetchResponse($args);

        $childClassParts = explode('\\', $childClass);
        $childClassBasename = end($childClassParts);

        /** @var class-string<TResult> $resultClass */
        $resultClass = "{$childClass}\\{$childClassBasename}Result";
        assert(class_exists($resultClass));

        return $resultClass::fromResponse($response);
    }

    /**
     * Send an operation through the client and return the response.
     *
     * @param  array<mixed>  $args
     */
    protected static function fetchResponse(array $args): Response
    {
        $endpointConfig = Configuration::endpoint(static::config(), static::endpoint());

        $document = static::document();
        $variables = static::variables($args);

        $endpointConfig->handleEvent(new StartRequest($document, $variables));

        $client = self::$clients[static::class] ??= $endpointConfig->makeClient();
        $response = $client->request($document, $variables);

        $endpointConfig->handleEvent(new ReceiveResponse($response));

        return $response;
    }

    /** @param array<mixed> $args */
    protected static function variables(array $args): \stdClass
    {
        $variables = new \stdClass();
        $arguments = static::converters();
        foreach ($args as $index => $arg) {
            if ($arg === ObjectLike::UNDEFINED) {
                continue;
            }

            [$name, $typeConverter] = $arguments[$index];
            $variables->{$name} = $typeConverter->toGraphQL($arg);
        }

        return $variables;
    }

    /** @return static&MockInterface */
    public static function mock(): MockInterface
    {
        // @phpstan-ignore return.type,assign.propertyType (I solemnly swear the type of MockInterface matches)
        return self::$mocks[static::class] ??= \Mockery::mock(static::class);
    }

    public static function clearMocks(): void
    {
        self::$mocks = [];
    }

    public static function requireMocks(bool $value): void
    {
        self::$requireMocks = $value;
    }

    public static function setClient(?Client $client): void
    {
        self::$clients[static::class] = $client;
    }

    public static function clearClients(): void
    {
        self::$clients = [];
    }
}
