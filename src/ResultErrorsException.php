<?php


namespace Spawnia\Sailor;


use Throwable;

class ResultErrorsException extends \Exception
{
    public function __construct(array $errors)
    {
        parent::__construct(\Safe\json_encode($errors));
    }
}
