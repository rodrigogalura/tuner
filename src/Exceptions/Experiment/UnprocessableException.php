<?php

namespace Laradigs\Tweaker\Exceptions\Experiment;

class UnprocessableException extends \Exception
{
    public function __construct(string $message = 'Unprocessable Entity', int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
