<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Executor\ExecutionResult;
use Psr\Http\Message\ResponseInterface;
use Safe\Exceptions\JsonException;

/**
 * Represents a response sent by a GraphQL server.
 *
 * During instantiation, the response structure is validated.
 * That does guarantee the server at least sent a syntactically correct response,
 * although it does not guarantee the content matches the sent query.
 */
class Response
{
    /**
     * The result of the execution of the requested operation.
     */
    public ?\stdClass $data;

    /**
     * A non‐empty list of errors, where each error is a map.
     *
     * @var array<int, \stdClass>|null
     */
    public ?array $errors;

    /**
     * This entry, if set, must have a map as its value.
     */
    public ?\stdClass $extensions;

    public static function fromResponseInterface(ResponseInterface $response): self
    {
        if (200 !== $response->getStatusCode()) {
            throw new InvalidDataException("Response must have status code 200, got: {$response->getStatusCode()}");
        }

        return self::fromJson(
            $response->getBody()->getContents()
        );
    }

    public static function fromExecutionResult(ExecutionResult $executionResult): self
    {
        return self::fromJson(
            \Safe\json_encode($executionResult->toArray())
        );
    }

    public static function fromJson(string $json): self
    {
        try {
            $response = \Safe\json_decode($json);
        } catch (JsonException $jsonException) {
            throw new InvalidDataException("Received a response that is invalid JSON: {$json}", 0, $jsonException);
        }

        if (! $response instanceof \stdClass) {
            throw new InvalidDataException("A response to a GraphQL operation must be a map, got: {$json}");
        }

        return self::fromStdClass($response);
    }

    public static function fromStdClass(\stdClass $rawResponse): self
    {
        $hasData = property_exists($rawResponse, 'data');
        $hasErrors = property_exists($rawResponse, 'errors');

        if (! $hasData && ! $hasErrors) {
            throw new InvalidDataException('A valid GraphQL response must contain either "data" or "errors", got: ' . \Safe\json_encode($rawResponse));
        }

        $instance = new self();

        if ($hasErrors) {
            $errors = $rawResponse->errors;
            self::validateErrors($errors);

            $instance->errors = $errors;
        }

        if ($hasData) {
            $data = $rawResponse->data;
            self::validateData($data);

            $instance->data = $data;
        }

        if (property_exists($rawResponse, 'extensions')) {
            $extensions = $rawResponse->extensions;
            self::validateExtensions($extensions);

            $instance->extensions = $extensions;
        }

        return $instance;
    }

    /**
     * Throw an exception if errors are present in the result.
     *
     * @throws \Spawnia\Sailor\ResultErrorsException
     *
     * @return $this
     */
    public function assertErrorFree(): self
    {
        if (isset($this->errors)) {
            throw new ResultErrorsException($this->errors);
        }

        return $this;
    }

    /**
     * Ensure that the "errors" are in a spec-compliant format.
     *
     * @param  mixed  $errors  whatever came from the API under the key "errors"
     *
     * @throws \Exception
     */
    protected static function validateErrors($errors): void
    {
        if (! is_array($errors)) {
            throw new InvalidDataException('The response entry "errors" must be a list if present, got: ' . \Safe\json_encode($errors));
        }

        if (0 === count($errors)) {
            throw new InvalidDataException('The response entry "errors" must not be empty if present, got: ' . \Safe\json_encode($errors));
        }

        foreach ($errors as $error) {
            if (! $error instanceof \stdClass) {
                throw new InvalidDataException('Each error in the response must be a map, got: ' . \Safe\json_encode($error));
            }

            if (! property_exists($error, 'message')) {
                throw new InvalidDataException('Each error in the response must contain a key "message", got: ' . \Safe\json_encode($error));
            }

            if (! is_string($error->message)) {
                throw new InvalidDataException('Each error in the response must contain a key "message" that is a string, got: ' . \Safe\json_encode($error));
            }
        }
    }

    /**
     * Ensure that the "data" is in a spec-compliant format.
     *
     * @param  mixed  $data  whatever came from the API under the key "data"
     *
     * @throws \Exception
     */
    protected static function validateData($data): void
    {
        if (
            $data instanceof \stdClass
            || null === $data
        ) {
            return;
        }

        throw new InvalidDataException('The response entry "data" must be a map or "null", got: ' . \Safe\json_encode($data));
    }

    /**
     * Ensure that the "extensions" are in a spec-compliant format.
     *
     * @param  mixed  $extensions  whatever came from the API under the key "extensions"
     *
     * @throws \Exception
     */
    protected static function validateExtensions($extensions): void
    {
        if (! $extensions instanceof \stdClass) {
            throw new InvalidDataException('The response entry "extensions" must be a map.');
        }
    }
}
