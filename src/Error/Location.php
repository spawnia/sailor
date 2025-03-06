<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

/** Beginning point of the syntax element in the GraphQL document associated with the error. */
class Location
{
    public static function fromStdClass(\stdClass $location): self
    {
        $instance = new static();

        $line = $location->line;
        if (! is_int($line)) {
            $lineType = gettype($line);
            throw new InvalidErrorException("Expected location.line to be an int, got: {$lineType}.");
        }
        $instance->line = $line;

        $column = $location->column;
        if (! is_int($column)) {
            $columnType = gettype($column);
            throw new InvalidErrorException("Expected location.column to be an int, got: {$columnType}.");
        }
        $instance->column = $column;

        return $instance;
    }

    /** Line number starting from 1. */
    public int $line;

    /** Column number starting from 1. */
    public int $column;
}
