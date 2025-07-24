<?php

namespace Laradigs\Tweaker;

class InvalidClientInput extends \Exception
{
    public function __construct($clientInput, string $message)
    {
        parent::__construct("The {$clientInput} is invalid. {$message}", 422);
    }
}
