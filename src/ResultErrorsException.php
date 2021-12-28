<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Exception;
use Spawnia\Sailor\Error\Error;
use stdClass;

class ResultErrorsException extends Exception
{
    /**
     * @param array<int, stdClass|Error> $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(\Safe\json_encode($errors));
    }
}
