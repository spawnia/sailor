<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

class ResultErrorsException extends \Exception
{
    /**
     * @param  \stdClass[]  $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(\Safe\json_encode($errors));
    }
}
