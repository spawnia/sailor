<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * @property TypedObject|null $data The result of executing the requested operation.
 */
abstract class Result
{
    /**
     * A non‐empty list of errors or `null` if there are no errors.
     *
     * Each error is a map that is guaranteed to contain at least
     * the key `message` and may contain arbitrary other keys.
     *
     * @var array<int, \stdClass>|null
     */
    public ?array $errors;

    /**
     * Optional, can be an arbitrary map if present.
     */
    public ?\stdClass $extensions;

    /**
     * Decode the raw data into proper types and set it.
     */
    abstract protected function setData(\stdClass $data): void;

    /**
     * Throws if errors are present in the result or returns an error free result.
     */
    abstract public function errorFree(): ErrorFreeResult;

    /**
     * @return static
     */
    public static function fromResponse(Response $response): self
    {
        $instance = new static();

        $instance->errors = $response->errors ?? null;
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
    public static function fromStdClass(\stdClass $stdClass): self
    {
        return static::fromResponse(
            Response::fromStdClass($stdClass)
        );
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
}
