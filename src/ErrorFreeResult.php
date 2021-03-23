<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * @property TypedObject|null $data The result of executing the requested operation.
 */
abstract class ErrorFreeResult
{
    /**
     * Optional, can be an arbitrary map if present.
     */
    public ?\stdClass $extensions;

    /**
     * @throws \Spawnia\Sailor\ResultErrorsException
     * @return static
     */
    public static function fromResult(Result $result): self
    {
        if (isset($result->errors)) {
            throw new ResultErrorsException($result->errors);
        }

        $instance = new static;

        $instance->data = $result->data;
        $instance->extensions = $result->extensions ?? null;

        return $instance;
    }
}
