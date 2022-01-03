<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Error\ResultErrorsException;

/**
 * @property \Spawnia\Sailor\ObjectLike|null $data The result of executing the requested operation.
 */
abstract class ErrorFreeResult
{
    /**
     * Optional, can be an arbitrary map if present.
     */
    public ?\stdClass $extensions;

    /**
     * @throws \Spawnia\Sailor\Error\ResultErrorsException
     *
     * @return static
     */
    public static function fromResult(Result $result): self
    {
        if (isset($result->errors)) {
            $exception = new ResultErrorsException($result->errors);
            $exception->isClientSafe = Configuration::endpoint($result::endpoint())
                ->errorsAreClientSafe();

            throw $exception;
        }

        $instance = new static();

        $instance->data = $result->data;
        $instance->extensions = $result->extensions ?? null;

        return $instance;
    }
}
