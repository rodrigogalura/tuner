<?php

namespace Laradigs\Tweaker\Exceptions\Experiment;

class NoDefinedColumnsException extends \Exception
{
    public function __construct(string $message = 'No defined columns', int $code = 51, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
