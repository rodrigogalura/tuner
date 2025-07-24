<?php

namespace Laradigs\Tweaker\Projection\Exceptions;

class CannotUseMultipleProjectionException extends \Exception
{
    public function __construct(array $projectionKeys)
    {
        parent::__construct('Cannot used ' . implode(' and ', $projectionKeys) . ' at the same time.', 422);
    }
}
