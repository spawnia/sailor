<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Error\ResultErrorsException;
use stdClass;

/**
 * @property \Spawnia\Sailor\ObjectLike|null $data The result of executing the requested operation.
 */
abstract class ErrorFreeResult
{
    /**
     * Optional, can be an arbitrary map if present.
     */
    public ?stdClass $extensions;

    /**
     * @throws \Spawnia\Sailor\Error\ResultErrorsException
     *
     * @return static
     */
    public static function fromResult(Result $result): self
    {
        if (isset($result->errors)) {
            throw new ResultErrorsException($result->errors, $result::config(), $result::endpoint());
        }

        $instance = new static();

        $instance->data = $result->data;
        $instance->extensions = $result->extensions ?? null;

        return $instance;
    }
}
