<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use GraphQL\Error\ClientAware;

/** Representation of an error according to https://spec.graphql.org/October2021/#sec-Errors. */
class Error extends \Exception implements ClientAware
{
    use OriginatesFromEndpoint;

    /**
     * Description of the error intended for the developer as a guide to understand and correct the error.
     *
     * @var string
     */
    public $message; // @phpstan-ignore property.phpDocType (will always be string)

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

    /** Arbitrary additional information. */
    public ?\stdClass $extensions;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function fromStdClass(\stdClass $error): self
    {
        $instance = new static($error->message);

        $locations = $error->locations ?? null;

        $instance->locations = is_array($locations) // @phpstan-ignore assign.propertyType (not validating array keys)
            ? array_map(
                [Location::class, 'fromStdClass'], // @phpstan-ignore argument.type (will throw if not \stdClass)
                $locations
            )
            : null;

        $path = $error->path ?? null;
        if (! is_array($path) && $path !== null) {
            $pathType = gettype($path);
            throw new InvalidErrorException("Expected path to be array or null, got {$pathType}.");
        }
        $instance->path = $path; // @phpstan-ignore assign.propertyType (not validating array elements)

        $extensions = $error->extensions ?? null;
        if (! $extensions instanceof \stdClass && $extensions !== null) {
            $extensionsType = gettype($extensions);
            throw new InvalidErrorException("Expected extensions to be \stdClass or null, got {$extensionsType}.");
        }
        $instance->extensions = $extensions;

        return $instance;
    }

    /**
     * If present, append the `debugMessage` returned by GraphQL servers implemented with `webonyx/graphql-php`.
     *
     * @see \GraphQL\Error\FormattedError::addDebugEntries
     */
    public function messageWithOptionalDebugMessage(): string
    {
        $maybeDebugMessage = $this->extensions->debugMessage ?? null;

        return is_string($maybeDebugMessage) && $maybeDebugMessage !== ''
            ? "{$this->message} ({$maybeDebugMessage})"
            : $this->message;
    }
}
