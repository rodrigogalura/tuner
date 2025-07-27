<?php

namespace Laradigs\Tweaker\Exceptions\Experiment;

class SomeNotInVisibleColumnsException extends \Exception
{
    public function __construct(string $message = 'Not in visible columns', int $code = 52, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
