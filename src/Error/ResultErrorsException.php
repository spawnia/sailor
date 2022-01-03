<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use Exception;
use GraphQL\Error\ClientAware;

class ResultErrorsException extends Exception implements ClientAware
{
    use WrapsEndpointError;

    /**
     * @var array<int, Error>
     */
    public array $errors;

    /**
     * @param array<int, Error> $errors
     */
    public function __construct(array $errors)
    {
        $messages = array_map(static fn (Error $error): string => $error->message, $errors);

        parent::__construct(implode(' | ', $messages));
    }
}
