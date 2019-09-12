<?php

namespace Spawnia\Sailor;

class Decoder
{
    /**
     * @param  Response  $response
     * @param  string  $class
     * @return object of type $class
     */
    public static function into(Response $response, string $class)
    {
        $instance = new $class;

        $instance->errors = $response->errors;
        $instance->extensions = $response->extensions;

        self::recurse($response->data, $instance);

        return $instance;
    }

    protected static function recurse(?\stdClass $data, object &$instance): void
    {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                $instance->{$key} []= $value;
                continue;
            }


        }
    }
}
