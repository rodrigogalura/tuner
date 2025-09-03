<?php

namespace Laradigs\Tweaker\V33\ValueObjects;

class Parser
{
    public function __construct(protected mixed $value)
    {
        //
    }

    public function assignIfEq($compareTo, $newValue, bool $strict = true): self
    {
        if ($strict
            ? $this->value === $compareTo
            : $this->value == $compareTo
        ) {
            $this->value = $newValue;
        }

        return $this;
    }

    public function get()
    {
        return $this->value;
    }
}
