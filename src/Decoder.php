<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

class Decoder
{
    /**
     * @param  Response  $response
     * @param  string  $class
     * @return TypedObject of type $class
     */
    public function into(Response $response, string $class): TypedObject
    {
        $instance = new $class;

        $instance->errors = $response->errors;
        $instance->extensions = $response->extensions;

        self::recurse($response->data, $instance);

        return $instance;
    }

    protected function recurse(?\stdClass $data, TypedObject &$instance): void
    {
        foreach ($data as $key => $valueOrValues) {
            $typeMapper = $instance->type($key);

            if (is_array($valueOrValues)) {
                foreach ($valueOrValues as $value) {
                    $converted = $typeMapper->map($value);

                    if ($converted instanceof TypedObject) {
                        $this->recurse($value, $converted);
                    }

                    $instance->{$key} [] = $converted;
                }
                continue;
            }

            $instance->{$key} = $typeMapper->map($valueOrValues);
        }
    }
}
