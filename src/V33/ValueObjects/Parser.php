<?php

namespace Tuner\Tuner\V33\ValueObjects;

class Parser
{
    public function __construct(protected mixed $value)
    {
        //
    }

    public function assignIfEqTo($compareTo, $newValue, bool $strict = true): self
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

    public static function create($value)
    {
        return new static($value);
    }
}
