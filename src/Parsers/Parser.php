<?php

namespace Tuner\Parsers;

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

    public function set($newValue)
    {
        $this->value = $newValue;
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
