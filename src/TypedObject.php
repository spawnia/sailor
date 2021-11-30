<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Introspection;
use Spawnia\Sailor\Codegen\FieldTypeMapper;
use stdClass;

abstract class TypedObject implements TypeConverter
{
    public string $__typename;

    /**
     * Construct a new instance of itself using plain data.
     *
     * @return static
     */
    public static function fromStdClass(stdClass $data): self
    {
        static $converter;
        $converter ??= new static;

        return $converter->fromGraphQL($data);
    }

    public function fromGraphQL($value)
    {
        $instance = new static;

        if (! $value instanceof stdClass) {
            throw new \InvalidArgumentException('Expected stdClass, got: ' . gettype($value));
        }

        foreach ($value as $field => $valueOrValues) {
            if ($field === Introspection::TYPE_NAME_FIELD_NAME) {
                // Short circuit here since this field is always present and needs no cast
                $instance->__typename = $valueOrValues;
                continue;
            } else {
                // The ClassGenerator placed methods for each property that return
                // a callable, which can map a value to its internal type
                $methodName = FieldTypeMapper::methodName($field);

                if (! method_exists(static::class, $methodName)) {
                    $availableMethods = array_diff(
                        get_class_methods(static::class),
                        get_class_methods(self::class),
                    );

                    $availableFields = implode(
                        ', ',
                        array_map(
                            [FieldTypeMapper::class, 'fieldName'],
                            $availableMethods,
                        )
                    );

                    throw new InvalidResponseException("Unknown field {$field}, available fields: {$availableFields}.");
                }

                /** @var TypeConverter $typeConverter */
                $typeConverter = $instance->{$methodName}();

                $converted = $typeConverter->fromGraphQL($valueOrValues);
            }

            $instance->{$field} = $converted;
        }

        return $instance;
    }

    public function toGraphQL($value)
    {
        throw new \Exception('Should never happen');
    }
}
