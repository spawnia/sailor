<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Error\ResultErrorsException;
use stdClass;

/**
 * @property \Spawnia\Sailor\ObjectLike|null $data The result of executing the requested operation.
 */
abstract class Result
{
    /**
     * A non‐empty list of errors or `null` if there are no errors.
     *
     * Each error is a map that is guaranteed to contain at least
     * the key `message` and may contain arbitrary other keys.
     *
     * @var array<int, Error>|null
     */
    public ?array $errors = null;

    /**
     * Optional, can be an arbitrary map if present.
     */
    public ?stdClass $extensions = null;

    /**
     * Decode the raw data into proper types and set it.
     */
    abstract protected function setData(stdClass $data): void;

    /**
     * Throws if errors are present in the result or returns an error free result.
     */
    abstract public function errorFree(): ErrorFreeResult;

    /**
     * The configured endpoint the result belongs to.
     */
    abstract public static function endpoint(): string;

    /**
     * @return static
     */
    public static function fromResponse(Response $response): self
    {
        $instance = new static();

        $instance->errors = isset($response->errors)
            ? static::parseErrors($response->errors)
            : null;
        $instance->extensions = $response->extensions ?? null;

        if (isset($response->data)) {
            $instance->setData($response->data);
        } else {
            $instance->data = null;
        }

        return $instance;
    }

    /**
     * @return static
     */
    public static function fromStdClass(stdClass $stdClass): self
    {
        return static::fromResponse(
            Response::fromStdClass($stdClass)
        );
    }

    /**
     * Useful for instantiation of failed mocked results.
     *
     * @param array<int, stdClass> $errors
     *
     * @return static
     */
    public static function fromErrors(array $errors): self
    {
        $instance = new static();
        $instance->errors = static::parseErrors($errors);

        return $instance;
    }

    /**
     * @param array<int, stdClass> $errors
     *
     * @return array<int, Error>
     */
    protected static function parseErrors(array $errors): array
    {
        $endpoint = Configuration::endpoint(static::endpoint());

        return array_map(
            static function (stdClass $raw) use ($endpoint): Error {
                $parsed = $endpoint->parseError($raw);
                $parsed->isClientSafe = $endpoint->errorsAreClientSafe();

                return $parsed;
            },
            $errors
        );
    }

    /**
     * Throw an exception if errors are present in the result.
     *
     * @throws \Spawnia\Sailor\Error\ResultErrorsException
     *
     * @return $this
     */
    public function assertErrorFree(): self
    {
        if (isset($this->errors)) {
            $exception = new ResultErrorsException($this->errors);
            $exception->isClientSafe = Configuration::endpoint(static::endpoint())
                ->errorsAreClientSafe();

            throw $exception;
        }

        return $this;
    }
}
