<?php

namespace Tuner\Exceptions;

use Exception;

class TunerException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
