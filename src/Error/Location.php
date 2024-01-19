<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

/**
 * Beginning point of the syntax element in the GraphQL document associated with the error.
 */
class Location
{
    public static function fromStdClass(\stdClass $location): self
    {
        $instance = new static();

        $instance->line = $location->line;
        $instance->column = $location->column;

        return $instance;
    }

    /** Line number starting from 1. */
    public int $line;

    /** Column number starting from 1. */
    public int $column;
}
