<?php

namespace Tuner\Parsers;

/**
 * @internal
 */
class Parser
{
    public function __construct(private mixed $value)
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
