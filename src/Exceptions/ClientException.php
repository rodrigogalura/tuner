<?php

namespace Tuner\Exceptions;

use Exception;

class ClientException extends Exception
{
    public function __construct($message, $code = 422, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
