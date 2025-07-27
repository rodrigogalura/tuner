<?php

namespace Laradigs\Tweaker\Exceptions\Experiment;

class ProjectionDisabledException extends \Exception
{
    public function __construct(string $message = 'Projection is disabled', int $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
