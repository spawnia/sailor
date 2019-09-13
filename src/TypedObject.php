<?php

namespace Spawnia\Sailor;

use Spawnia\Sailor\Codegen\ClassGenerator;

abstract class TypedObject
{
    /**
     * @param  \stdClass  $data
     * @return static
     */
    public static function fromStdClass(\stdClass $data): self
    {
        $instance = new static;

        foreach ($data as $key => $valueOrValues) {
            // The ClassGenerator placed methods for each property that return
            // a callable, which can map a value to its internal type
            $methodName = ClassGenerator::typeDiscriminatorMethodName($key);
            $typeMapper = $instance->{$methodName}($key);

            if (is_array($valueOrValues)) {
                foreach ($valueOrValues as $value) {
                    $instance->{$key} [] = $typeMapper($value);
                }
                continue;
            }

            $instance->{$key} = $typeMapper($valueOrValues);
        }

        return $instance;
    }
}
