<?php

namespace Laradigs\Tweaker\Exceptions\Experiment;

class SomeNotInProjectableColumnsException extends \Exception
{
    public function __construct(string $message = 'Not in projectable columns', int $code = 52, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
