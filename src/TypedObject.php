<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Codegen\ClassGenerator;

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

        foreach ($data as $key => $valueOrValues) {
            if(is_null($valueOrValues)) {
                $converted = null;
            } else {
                // The ClassGenerator placed methods for each property that return
                // a callable, which can map a value to its internal type
                $methodName = ClassGenerator::typeDiscriminatorMethodName($key);
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

            $instance->{$key} = $converted;
        }

        return $instance;
    }
}
