<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * The result of the execution of the requested operation.
     *
     * @var \stdClass|null
     */
    public $data;

    /**
     * A non‐empty list of errors, where each error is a map.
     *
     * @var \stdClass[]|null
     */
    public $errors;

    /**
     * This entry, if set, must have a map as its value.
     *
     * @var \stdClass|null
     */
    public $extensions;

    public static function fromResponseInterface(ResponseInterface $response): self
    {
        return self::fromJson($response->getBody());
    }

    public static function fromJson(string $json): self
    {
        $response = \Safe\json_decode($json);

        if (! $response instanceof \stdClass) {
            throw new \Exception('A response to a GraphQL operation must be a map.');
        }

        return self::fromStdClass($response);
    }

    public static function fromStdClass(\stdClass $stdClass): self
    {
        $hasData = property_exists($stdClass, 'data');
        $hasErrors = property_exists($stdClass, 'errors');

        if (! $hasData && ! $hasErrors) {
            throw new \Exception('A valid GraphQL response must contain either "data" or "errors".');
        }

        $instance = new self;

        if ($hasErrors) {
            $errors = $stdClass->errors;

            self::validateErrors($errors);

            $instance->errors = $errors;
        }

        if ($hasData) {
            $data = $stdClass->data;

            self::validateData($data);

            $instance->data = $data;
        }

        if (property_exists($stdClass, 'extensions')) {
            $extensions = $stdClass->extensions;

            self::validateExtensions($extensions);

            $instance->extensions = $extensions;
        }

        // TODO validate that no other entries are in the response

        return $instance;
    }

    protected static function validateErrors($errors): void
    {
        if (! is_array($errors)) {
            throw new \Exception('The response entry "errors" must be a list if present.');
        }

        if (count($errors) === 0) {
            throw new \Exception('The response entry "errors" must not be empty if present.');
        }

        foreach ($errors as $error) {
            if (! $error instanceof \stdClass) {
                throw new \Exception('Each error in the response must be a map.');
            }

            if (! property_exists($error, 'message')) {
                throw new \Exception('Each error in the response must contain a key "message".');
            }

            if (! is_string($error->message)) {
                throw new \Exception('Each error in the response must contain a key "message" that is a string.');
            }
        }
    }

    protected static function validateData($data): void
    {
        if (
            ! $data instanceof \stdClass
            || $data === null
        ) {
            throw new \Exception('The response entry "data" must be a map or "null".');
        }
    }

    protected static function validateExtensions($extensions): void
    {
        if (! $extensions instanceof \stdClass) {
            throw new \Exception('The response entry "extensions" must be a map.');
        }
    }
}
