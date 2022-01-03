<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use Exception;
use GraphQL\Error\ClientAware;
use stdClass;

class ResultErrorsException extends Exception implements ClientAware
{
    use WrapsEndpointError;

    /**
     * @param array<int, stdClass|Error> $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(\Safe\json_encode($errors));
    }
}
