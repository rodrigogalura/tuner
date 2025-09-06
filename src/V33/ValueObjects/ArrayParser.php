<?php

namespace Laradigs\Tweaker\V33\ValueObjects;

class ArrayParser extends Parser
{
    public function __construct(array $value)
    {
        parent::__construct($value);
    }

    public function sanitize(): self
    {
        $this->value = array_filter(array_map('trim', $this->value));

        return $this;
    }

    public function intersectTo(array $to): self
    {
        $this->value = array_intersect($this->value, $to);

        return $this;
    }
}
