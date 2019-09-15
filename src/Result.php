<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * @property TypedObject|null $data The result of the execution of the requested operation.
 */
abstract class Result
{
    /**
     * A non‐empty list of errors, where each error is a map.
     *
     * @var \stdClass[]|null
     */
    public $errors;

    /**
     * This entry, if set, must have a map as its value.
     *
     * @var \stdClass|null
     */
    public $extensions;

    /**
     * Decode the raw data into proper types and set it.
     *
     * @param  \stdClass  $data
     * @return void
     */
    abstract protected function setData(\stdClass $data): void;

    public static function fromResponse(Response $response)
    {
        $instance = new static;

        $instance->errors = $response->errors;
        $instance->extensions = $response->extensions;

        if (is_null($response->data)) {
            $instance->data = null;
        } else {
            $instance->setData($response->data);
        }

        return $instance;
    }

    /**
     * Throw if any errors are present in the result.
     *
     * @throws ResultErrorsException
     */
    public function throwErrors(): void
    {
        if (! $this->errors) {
            return;
        }

        throw new ResultErrorsException($this->errors);
    }
}
