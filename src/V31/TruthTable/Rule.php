<?php

namespace Laradigs\Tweaker\V31\TruthTable;

use Laradigs\Tweaker\V31\ErrorCodes;
use Laradigs\Tweaker\V31\Projection\Error;

abstract class Rule
{
    public function __construct(private Error|ErrorCodes $e)
    {
        //
    }

    public function getErrorCode()
    {
        return $this->e->value;
    }

    public function failed(string $subject)
    {
        return ! $this->passed($subject);
    }

    abstract public function passed(string $subject);
}
