<?php

namespace Laradigs\Tweaker\V31\TruthTable;

abstract class Rule
{
    public function __construct(private int $errorCode = 0)
    {
        //
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function failed($subject)
    {
        return !$this->passed($subject);
    }

    abstract public function passed(string $subject);
}
