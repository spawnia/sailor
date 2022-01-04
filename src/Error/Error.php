<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use Exception;
use GraphQL\Error\ClientAware;
use stdClass;

/**
 * Representation of an error according to https://spec.graphql.org/October2021/#sec-Errors.
 */
class Error extends Exception implements ClientAware
{
    use OriginatesFromEndpoint;

    /**
     * Description of the error intended for the developer as a guide to understand and correct the error.
     *
     * @var string
     */
    // @phpstan-ignore-next-line overridden type does not match, but actually will always be string
    public $message;

    /**
     * Beginning points of syntax elements in the GraphQL document associated with the error.
     *
     * @var array<int, Location>|null
     */
    public ?array $locations;

    /**
     * Path of the response field which experienced the error.
     *
     * @var array<int, string|int>|null
     */
    public ?array $path;

    /**
     * Arbitrary additional information.
     */
    public ?stdClass $extensions;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function fromStdClass(stdClass $error): self
    {
        $instance = new static($error->message);

        $instance->locations = isset($error->locations)
            ? array_map([Location::class, 'fromStdClass'], $error->locations)
            : null;
        $instance->path = $error->path ?? null;
        $instance->extensions = $error->extensions ?? null;

        return $instance;
    }
}
