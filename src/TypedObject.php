<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Codegen\FieldTypeMapper;

abstract class TypedObject
{
    /**
     * Construct a new instance of itself using plain data.
     *
     * @return static
     */
    public static function fromStdClass(\stdClass $data): self
    {
        $instance = new static;

        foreach ($data as $field => $valueOrValues) {
            if (is_null($valueOrValues)) {
                $converted = null;
            } else {
                // The ClassGenerator placed methods for each property that return
                // a callable, which can map a value to its internal type
                $methodName = Codegen\FieldTypeMapper::methodName($field);

                $thisFunctionItself = __FUNCTION__;
                $availableMethods = array_filter(
                    get_class_methods(static::class),
                    static fn (string $method): bool => $method !== $thisFunctionItself,
                );
                if (! in_array($methodName, $availableMethods)) {
                    $availableFields = implode(
                        ', ',
                        array_map(
                            [FieldTypeMapper::class, 'fieldName'],
                            $availableMethods,
                        )
                    );

                    throw new InvalidResponseException("Unknown field {$field}, available fields: {$availableFields}.");
                }

                $typeMapper = $instance->{$methodName}();

                if (is_array($valueOrValues)) {
                    $converted = [];
                    foreach ($valueOrValues as $value) {
                        $converted [] = $typeMapper($value);
                    }
                } else {
                    $converted = $typeMapper($valueOrValues);
                }
            }

            $instance->{$field} = $converted;
        }

        return $instance;
    }
}
