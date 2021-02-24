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
     * @return static
     */
    public static function fromResponse(Response $response): self
    {
        $instance = new static;

        $instance->errors = $response->errors ?? null;
        $instance->extensions = $response->extensions ?? null;

        if (is_null($response->data)) {
            $instance->data = null;
        } else {
            $instance->setData($response->data);
        }

        return $instance;
    }

    /**
     * Throw an exception if errors are present in the result.
     *
     * @throws \Spawnia\Sailor\ResultErrorsException
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
