<?php

namespace RGalura\ApiIgniter\Exceptions;

class MinimumKeywordException extends \Exception
{
    /**
     * Create a new exception for below the minimum character.
     *
     * @param  int  $minimum
     */
    public function __construct($minimum, $strict = 0)
    {
        parent::__construct("Keyword characters must be at least {$minimum} length", $strict);
    }
}
